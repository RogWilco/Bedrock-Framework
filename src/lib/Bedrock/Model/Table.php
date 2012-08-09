<?php
namespace Bedrock\Model;

/**
 * Represents a table in the database, allowing basic table operations and
 * storing table-specific properties.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 08/29/2008
 * @updated 07/02/2012
 */
class Table extends \Bedrock\Model {
	const STATE_UNCHANGED = 0;
	const STATE_CHANGED = 1;
	const STATE_NEW = 2;
	
	const TYPE_TABLE = 0;
	const TYPE_MAP = 1;
	const TYPE_VIEW = 2;
	
	const MAP_TYPE_ONE_ONE = 0;
	const MAP_TYPE_ONE_MANY = 1;
	const MAP_TYPE_MANY_ONE = 2;
	const MAP_TYPE_MANY_MANY = 3;
	
	protected $_name;
	protected $_type;
	protected $_mappings;
	protected $_columns;
	protected $_columns_add;
	protected $_columns_drop;
	protected $_columns_alter;
	protected $_columns_insert;
	protected $_indexes;
	protected $_key_primary;
	protected $_keys_foreign;
	protected $_properties;
	protected $_defaults;
	protected $_state;
	
	/**
	 * Initializes the table object.
	 * 
	 * @param array $tableConfig the table configuration
	 * @param \Bedrock\Model\Database $database the Database object to use
	 */
	public function __construct($tableConfig = array(), $database = NULL) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			parent::__construct($database);
			
			$this->_name = $tableConfig['name'];
			$this->_properties = $tableConfig['properties'];
			// @todo handle column details in tableConfig
			$this->_state = self::STATE_NEW;
		}
		catch(\PDOException $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('The table object could not be initialized.');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('The table object could not be initialized.');
		}
	}
	
	/**
	 * Loads the table schema from the database.
	 */
	public function load() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			$resTable = $this->_connection->query('SHOW TABLE STATUS WHERE Name = \'' . self::sanitize($this->_name) . '\'');
			$resColumns = $this->_connection->query('SHOW FULL COLUMNS FROM ' . self::sanitize($this->_name));
			
			// Retrieve Table Info
			$this->_properties = array();
			$rowTable = $resTable->fetch(\PDO::FETCH_ASSOC);
			
			$this->_properties['engine'] = $rowTable['Engine'];
			$this->_properties['length'] = $rowTable['Data_length'];
			$this->_properties['rows'] = $rowTable['Rows'];
			$this->_properties['auto_increment'] = $rowTable['Auto_increment'];
			$this->_properties['collation'] = $rowTable['Collation'];
			$this->_properties['created'] = $rowTable['Create_time'];
			$this->_properties['updated'] = $rowTable['Update_time'];
			$this->_properties['comment'] = $rowTable['Comment'];
			
			if($rowTable['Comment'] != '' && substr($rowTable['Comment'], 0, 3) == 'map') {
				$this->_type = self::TYPE_MAP;
			}
			elseif($rowTable['Comment'] == 'VIEW') {
				$this->_type = self::TYPE_VIEW;
			}
			else {
				$this->_type = self::TYPE_TABLE;
				
				// Determine Mappings
				if(trim($rowTable['Comment'])) {
					$mappings = explode('|', $rowTable['Comment']);
					$mappings = explode(':', $mappings[1]);
					$mappings = explode(',', $mappings[1]);

					foreach($mappings as $mapping) {
						$table = substr($mapping, 0, strpos($mapping, '('));
						$matches = array();
						preg_match('#\((.*?)\)#', $mapping, $matches);

						\Bedrock\Common\Logger::info('Mapping with table "' . $table . '" found: ' . $matches[1]);

						switch($matches[1]) {
							case 'one_one':
								$type = self::MAP_TYPE_ONE_ONE;
								break;

							default:
							case 'one_many':
								$type = self::MAP_TYPE_ONE_MANY;
								break;

							case 'many_one':
								$type = self::MAP_TYPE_MANY_ONE;
								break;

							case 'many_many':
								$type = self::MAP_TYPE_MANY_MANY;
								break;
						}

						$this->_mappings[$table] = $type;
					}
				}
			}
			
			// Retrieve Column Info
			$this->_columns = array();
			$rows = $resColumns->fetchAll(\PDO::FETCH_ASSOC);
			
			foreach($rows as $row) {
				$columnData = array();
				$type = substr($row['Type'], 0, strpos($row['Type'], '('));
				
				if(!$type) {
					$type = $row['Type'];
				}

				$length = substr($row['Type'], strpos($row['Type'], '('));
				$length = substr($length, 1, strpos($length, ')') - 1);
				
				$columnData = array('name' => $row['Field'],
								'type' => $type,
								'length' => $length,
								'null' => ($row['Null'] != 'NO'),
								'default' => $row['Default'],
								'primary_key' => ($row['Key'] == 'PRI'));
				
				// Set Foreign Key Type (if any)
				if($row['Comment'] != '' && substr($row['Comment'], 0, 2) == 'fk') {
					$columnData['foreign_key'] = substr($row['Comment'], 3);
					
					switch($this->_mappings[$columnData['foreign_key']]) {
						case self::MAP_TYPE_ONE_ONE:
							$columnData['foreign_key_type'] = \Bedrock\Model\Column::FK_TYPE_ONE_TO_ONE;
							break;
							
						case self::MAP_TYPE_ONE_MANY:
							$columnData['foreign_key_type'] = \Bedrock\Model\Column::FK_TYPE_ONE_TO_MANY;
							break;
							
						case self::MAP_TYPE_MANY_ONE:
							$columnData['foreign_key_type'] = \Bedrock\Model\Column::FK_TYPE_MANY_TO_ONE;
							break;
							
						case self::MAP_TYPE_MANY_MANY:
							$columnData['foreign_key_type'] = \Bedrock\Model\Column::FK_TYPE_MANY_TO_MANY;
							break;
							
						default:
							$columnData['foreign_key_type'] = \Bedrock\Model\Column::FK_TYPE_NONE;
							break;
					}
				}
				else {
					$columnData['foreign_key_type'] = \Bedrock\Model\Column::FK_TYPE_NONE;
				}
				
				$columnObj = new \Bedrock\Model\Column($columnData);
				
				$this->_columns[] = $columnObj;
				
				if($columnData['primary_key']) {
					$this->_key_primary = $columnObj;
				}
				elseif($columnData['foreign_key']) {
					$this->_keys_foreign[$columnData['foreign_key']] = $columnObj;
				}
			}
			
			$this->_state = self::STATE_UNCHANGED;
		}
		catch(\PDOException $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('There was a problem retrieving the table information.');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('There was a problem retrieving the table information.');
		}
	}
	
	/**
	 * Saves the current table schema, creating a new table if it does not yet
	 * exist.
	 */
	public function save() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			if($this->_state == self::STATE_CHANGED) {
				// Update Table
				$sql = 'ALTER TABLE ' . self::sanitize($this->_name);
				
				// Columns: Add
				if(is_array($this->_columns_add) && count($this->_columns_add) > 0) {
					foreach($this->_columns_add as $newColumn) {
						$sql .= ' ADD COLUMN ' . $newColumn->definition . ',';
					}
				}
				
				// Columns: Insert
				if(is_array($this->_columns_insert) && count($this->_columns_insert) > 0) {
					foreach($this->_columns_insert as $insertColumnData) {
						$insertColumn = $insertColumnData['column'];
						$after = $insertColumnData['after'];
						
						$sql .= ' ADD COLUMN ' . $insertColumn->definition . ' AFTER ' . $after . ',';
					}
				}
				
				// Columns: Alter
				if(is_array($this->_columns_alter) && count($this->_columns_alter) > 0) {
					foreach($this->_columns_alter as $alterColumn) {
						$sql .= ' MODIFY COLUMN ' . $alterColumn->definition . ' ';
					}
				}
				
				// Columns: Drop
				if(is_array($this->_columns_drop) && count($this->_columns_drop) > 0) {
					foreach($this->_columns_drop AS $dropColumn) {
						$sql .= ' DROP COLUMN ' . $dropColumn->name . ',';
					}
				}
				
				// Table Properties
				$sql .= ' ENGINE = ' . $this->_properties['engine'] .
							', DEFAULT CHARACTER SET ' . $this->_properties['charset'] .
							', DEFAULT COLLATE ' . $this->_properties['collate'] .
							', COMMENT = \'' . self::sanitize($this->getMappingString()) . '\'';
				
				// Execute Query
				$this->_connection->exec($sql);
			}
			elseif($this->_state == self::STATE_NEW) {
				// Create Table
				$sql = 'CREATE TABLE ' . self::sanitize($this->_name) . ' (';
				
				// Columns
				foreach($this->_columns as $column) {
					$sql .= $column->definition . ', ';
				}
				
				$sql = substr($sql, 0, strlen($sql)-2) . ')';
				
				// Table Properties
				if($this->_properties['engine']) {
					$sql .= ' ENGINE = ' . $this->_properties['engine'];
				}
				
				if($this->_properties['charset']) {
					$sql .= ' DEFAULT CHARACTER SET = ' . $this->_properties['charset'];
				}
				
				if($this->_properties['collation']) {
					$sql .= ' DEFAULT COLLATE = ' . $this->_properties['collation'];
				}
				
				if($this->_properties['comment']) {
					$sql .= ' COMMENT = \'' . self::sanitize($this->getMappingString()) . '\'';
				}
				
				// Execute Query
				$this->_connection->exec($sql);
			}
			
			$this->_state = self::STATE_UNCHANGED;
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\PDOException $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('A problem was encountered while saving the table schema.');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('A problem was encountered while saving the table schema.');
		}
	}
	
	/**
	 * Drops the current table from the database.
	 */
	public function drop() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Execute Query
			$this->_connection->query('DROP TABLE IF EXISTS ' . self::sanitize($this->_name));
			
			$this->_state = self::STATE_NEW;
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\PDOException $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('A problem was encountered while dropping the table from the database.');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('A problem was encountered while dropping the table from the database.');
		}
	}
	
	/**
	 * Empties the table of all data and resets any auto increment fields.
	 */
	public function reset() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Execute Query
			$this->_connection->query('DELETE FROM ' . self::sanitize($this->_name));
			$this->_connection->query('ALTER TABLE ' . self::sanitize($this->_name) . ' AUTO_INCREMENT = 0');
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\PDOException $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('A problem was encountered while dropping the table from the database.');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('A problem was encountered while dropping the table from the database.');
		}
	}

	/**
	 * Attempts to revert a table from the specified backup copy.
	 *
	 * @param string $backupName the name of the backup table
	 *
	 * @throws \Bedrock\Model\Exception if the revert fails
	 * @return bool whether or not the revert was successful
	 */
	public function revert($backupName) {
		\Bedrock\Common\Logger::logEntry();

		try {
			// Setup
			$result = false;

			\Bedrock\Common\Logger::info('Reverting table "' . $this->_name . '" from backup "' . $backupName . '"...');
			$res = $this->_connection->query('CREATE TABLE ' . self::sanitize($this->_name) . ' SELECT * FROM ' . self::sanitize($backupName));
			
			if($res) {
				$res = $this->_connection->query('DROP TABLE IF EXISTS ' . self::sanitize($backupName));

				$result = (bool) $res;
			}

			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('Reverting a table from the backup table "' . $backupName . '" failed!');
		}
	}
	
	/**
	 * Returns an array of column objects for the current table.
	 *
	 * @return array an array of column objects
	 */
	public function getColumns() {
		return $this->_columns;
	}
	
	/**
	 * Adds a new column to the table.
	 *
	 * @param \Bedrock\Model\Column $newColumn the column to add to the table
	 */
	public function addColumn($newColumn) {
		$this->_columns_add[] = $newColumn;
		
		if($newColumn->foreign_key) {
			$this->_mappings[$newColumn->foreign_key] = $this->mappingTypeFromString($newColumn->fkTypeToString());
			$this->_keys_foreign[$newColumn->foreign_key] = $newColumn;
		}
		
		$this->_state = self::STATE_CHANGED;
	}
	
	/**
	 * Inserts a new column after the specified column index.
	 *
	 * @param \Bedrock\Model\Column $newColumn the column to add to the table
	 * @param integer $afterIndex the index after which to insert the column
	 */
	public function insertColumn($newColumn, $afterColumnName) {
		$this->_columns_insert[] = array('column' => $newColumn, 'after' => $afterColumnName);
		
		if($newColumn->foreign_key) {
			$this->_mappings[$newColumn->foreign_key] = $this->mappingTypeFromString($newColumn->fkTypeToString());
			$this->_keys_foreign[$newColumn->foreign_key] = $newColumn;
		}
		
		$this->_state = self::STATE_CHANGED;
	}
	
	/**
	 * Removes a column from the table.
	 * 
	 * @param string $columnName the name of the column to remove
	 */
	public function deleteColumn($columnName) {
		foreach($this->_columns as $key => $column) {
			if($column->name == $columnName) {
				$this->_columns_drop = $column;
				$this->_columns = array_diff_key($this->_columns, array($key => $column));
				
				if($column->foreign_key) {
					unset($this->_mappings[$column->foreign_key]);
					unset($this->_keys_foreign[$column->foreign_key]);
				}
				
				$this->_state = self::STATE_CHANGED;
				return;
			}
		}
	}
	
	/**
	 * Sets a custom property for the table (useful for properties not directly
	 * supported). If no value is specified, a default will be selected if
	 * available.
	 *
	 * @param string $propertyName the name of the property to set
	 * @param string $propertyValue the value for the property
	 */
	public function setProperty($propertyName, $propertyValue) {
		if($propertyValue == '') {
			$this->_properties[$propertyName] = $this->_defaults[$propertyName];
		}
		else {
			$this->_properties[$propertyName] = $propertyValue;
		}
		
		$this->_state = self::STATE_CHANGED;
	}
	
	/*
	 * Returns the requested property.
	 * 
	 * @param string $propertyName the name of the property to retrieve
	 * @return string the corresponding property value
	 */
	public function getProperty($propertyName) {
		// Setup
		$result = NULL;
		
		if($propertyName == 'name') {
			$result = $this->_name;
		}
		elseif($propertyName == 'type') {
			switch($this->_type) {
				case self::TYPE_TABLE:
					$result = 'TABLE';
					break;
					
				case self::TYPE_MAP:
					$result = 'MAP';
					break;
					
				case self::TYPE_VIEW:
					$result = 'VIEW';
					break;
					
				default:
					// Do Nothing
					break;
			}
		}
		elseif(array_key_exists($propertyName, $this->_properties)) {
			$result = $this->_properties[$propertyName];
		}
		
		return $result;
	}
	
	/**
	 * Retrieves the table's primary key.
	 *
	 * @return \Bedrock\Model\Column the primary key's Column object
	 */
	public function getPrimaryKey() {
		return $this->_key_primary;
	}
	
	/**
	 * Retrieves all foreign key columns in the table.
	 *
	 * @return array an array of all corresponding foreign key Column objects
	 */
	public function getForeignKeys() {
		return $this->_keys_foreign;
	}
	
	/**
	 * Returns the currently set table mappings.
	 *
	 * @return array an array containing the current table mappings
	 */
	public function getMappings() {
		return $this->_mappings;
	}
	
	/**
	 * Retrieves the mapping type based on the specified string.
	 *
	 * @param string $mappingTypeString the mapping type as a string
	 * @return integer the corresponding mapping type
	 */
	public function mappingTypeFromString($mappingTypeString) {
		// Setup
		$mappingType = NULL;
		
		switch($mappingTypeString) {
			case 'one_one':
				$mappingType = self::MAP_TYPE_ONE_ONE;
				break;
				
			case 'one_many':
				$mappingType = self::MAP_TYPE_ONE_MANY;
				break;
				
			case 'many_one':
				$mappingType = self::MAP_TYPE_MANY_ONE;
				break;
				
			case 'many_many':
				$mappingType = self::MAP_TYPE_MANY_MANY;
				break;
		}
		
		return $mappingType;
	}
	
	/**
	 * Builds the current table's mapping string based on its properties.
	 * 
	 * @return string the resulting mapping string
	 */
	public function getMappingString() {
		// Setup
		$mappingString = '';
		
		// Build Mapping String
		if($this->_type == self::TYPE_TABLE) {
			$mappingString = 'table';
		}
		else {
			$mappingString = 'map';
		}
		
		if(count($this->_mappings)) {
			$mappingString .= '|mappings:';
		}
		
		foreach($this->_mappings as $tableName => $mappingType) {
			$mappingString .= $tableName . '(';
			
			switch($mappingType) {
				case self::MAP_TYPE_ONE_ONE:
					$mappingString .= 'one_one';
					break;
					
				case self::MAP_TYPE_ONE_MANY:
					$mappingString .= 'one_many';
					break;
					
				case self::MAP_TYPE_MANY_ONE:
					$mappingString .= 'many_one';
					break;
					
				case self::MAP_TYPE_MANY_MANY:
					$mappingString .= 'many_many';
					break;
			}
			
			$mappingString .= '),';
		}
		
		$mappingString = substr($mappingString, 0, strlen($mappingString)-1);
		
		return $mappingString;
	}
	
	/**
	 * Returns the specified format type extension as a string.
	 *
	 * @param integer $format the format type to translate
	 * @return string the corresponding format extension
	 */
	public function formatToString($format) {
		// Setup
		$result = '';
		
		switch($format) {
			default:
				$result = '';
				break;
				
			case \Bedrock\Model::FORMAT_SQL:
				$result = 'sql';
				break;
				
			case \Bedrock\Model::FORMAT_XML:
				$result = 'xml';
				break;
				
			case \Bedrock\Model::FORMAT_YAML:
				$result = 'yaml';
				break;
				
			case \Bedrock\Model::FORMAT_CSV:
				$result = 'csv';
				break;
		}
		
		return $result;
	}

	/**
	 * Returns the current table's definition (including data) as a string using
	 * the default format.
	 *
	 * @return string the generated string
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Returns the current table's definition (including data) as a string using
	 * the specified format.
	 *
	 * @param string $format the output format to use
	 * @return string the generated string
	 */
	public function toString($format = \Bedrock\Model::FORMAT_SQL) {
		\Bedrock\Common\Logger::logEntry();

		try {
			// Setup
			$result = '';

			switch($format) {
				default:
				case \Bedrock\Model::FORMAT_SQL:
					$result .= $this->schemaToString(\Bedrock\Model::FORMAT_SQL) . "\n";
					$result .= $this->dataToString(\Bedrock\Model::FORMAT_SQL);
					break;

				case \Bedrock\Model::FORMAT_XML:
					$schema = \DOMDocument::loadXML($this->schemaToString(\Bedrock\Model::FORMAT_XML));
					$records = \DOMDocument::loadXML($this->dataToString(\Bedrock\Model::FORMAT_XML));
					//$records = DOMDocument::loadXML($records->saveXML($records->documentElement));

					$fragment = $schema->createDocumentFragment();
					$fragment->appendXML($records->saveXML($records->documentElement));
					$schema->documentElement->appendChild($fragment);
					$schema->formatOutput = true;

					$result = $schema->saveXML();
					break;

				case \Bedrock\Model::FORMAT_YAML:
					$yaml = \Bedrock\Common\Data\YAML::decode($this->schemaToString(\Bedrock\Model::FORMAT_YAML));
					$records = \Bedrock\Common\Data\YAML::decode($this->dataToString(\Bedrock\Model::FORMAT_YAML));
					
					foreach($records as $record) {
						$yaml['table']['records'] = $records['records'];
					}

					$result = new \Bedrock\Common\Data\YAML($yaml);
					$result = (string) $result;
					break;

				case \Bedrock\Model::FORMAT_CSV:
					$result .= $this->schemaToString(\Bedrock\Model::FORMAT_CSV) . "\n";
					$result .= $this->dataToString(\Bedrock\Model::FORMAT_CSV) . "\n";
					break;
			}

			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\DOMException $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}
	
	/**
	 * Returns the current table's schema as a string of the specified type.
	 *
	 * @param integer $formatType the format type to use
	 * @return string the schema represented as a string
	 */
	public function schemaToString($formatType = \Bedrock\Model::FORMAT_SQL) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = '';

			$this->load();
			
			switch($formatType) {
				default:
					
				// SQL Query
				case \Bedrock\Model::FORMAT_SQL:
					$result = 'CREATE TABLE `' . self::sanitize($this->_name) . '` (';
					
					// Table Columns
					foreach($this->_columns as $column) {
						$result .= $column->definition . ', ';
					}
					
					$result = substr($result, 0, strlen($result)-2) . ')';
					
					// Table Properties
					if($this->_properties['engine'] != '') {
						$result .= ' ENGINE = ' . $this->_properties['engine'];
					}
					
					if($this->_properties['charset'] != '') {
						$result .= ' DEFAULT CHARACTER SET = ' . $this->_properties['charset'];
					}
					
					if($this->_properties['collation'] != '') {
						$result .= ' DEFAULT COLLATE = ' . $this->_properties['collation'];
					}
					
					if($this->_properties['comment'] != '') {
						$result .= ' COMMENT = \'' . self::sanitize($this->_properties['comment']) . '\'';
					}
					break;
					
				// XML String
				case \Bedrock\Model::FORMAT_XML:
					$xml = new \SimpleXMLElement('<table></table>');
					$xml->addAttribute('name', $this->_name);
					
					// Table Properties
					$xml->addChild('properties');
					
					if($this->_properties['engine'] != '') {
						$xml->properties->addChild('engine', $this->_properties['engine']);
					}
					
					if($this->_properties['charset'] != '') {
						$xml->properties->addChild('charset', $this->_properties['charset']);
					}
					
					if($this->_properties['collation'] != '') {
						$xml->properties->addChild('collation', $this->_properties['collation']);
					}
					
					if($this->_properties['comment'] != '') {
						$xml->properties->addChild('comment', $this->_properties['comment']);
					}
					
					// Table Columns
					$xml->addChild('columns');
					
					foreach($this->_columns as $column) {
						$currentColumn = $xml->columns->addChild('column');
						$currentColumn->addAttribute('name', $column->name);
						$currentColumn->addChild('type', $column->type);
						$currentColumn->addChild('length', $column->length);
						$currentColumn->addChild('size', $column->size);
						$currentColumn->addChild('default', $column->default);
						$currentColumn->addChild('comment', $column->comment);
						
						$flags = $currentColumn->addChild('flags');
						$flags->addChild('primarykey', $column->primary_key ? 1 : 0);
						$flags->addChild('autoincrement', $column->auto_increment ? 1 : 0);
						$flags->addChild('unique', $column->unique ? 1 : 0);
						$flags->addChild('null', $column->null ? 1 : 0);
					}

					$dom = dom_import_simpleXml($xml)->ownerDocument;
					$dom->formatOutput = true;
					
					$result = $dom->saveXML();
					break;
					
				// YAML String
				case \Bedrock\Model::FORMAT_YAML:
					// Build Array
					$data = array(
						'table' => array(
							'name' => $this->_name,
							'properties' => array()
						)
					);

					// Table Properties
					if($this->_properties['engine'] != '') {
						$data['table']['properties']['engine'] = $this->_properties['engine'];
					}

					if($this->_properties['charset'] != '') {
						$data['table']['properties']['charset'] = $this->_properties['charset'];
					}

					if($this->_properties['collation'] != '') {
						$data['table']['properties']['collation'] = $this->_properties['collation'];
					}

					if($this->_properties['comment'] != '') {
						$data['table']['properties']['comment'] = $this->_properties['comment'];
					}

					// Table Columns
					foreach($this->_columns as $column) {
						$colArr = array();
						
						if($column->name != '') $colArr['name'] = $column->name;
						if($column->type != '') $colArr['type'] = $column->type;
						if($column->length != '') $colArr['length'] = $column->length;
						if($column->size != '') $colArr['size'] = $column->size;
						if($column->default != '') $colArr['default'] = $column->default;
						if($column->comment != '') $colArr['comment'] = $column->comment;

						$colArr['flags'] = array(
							'primarykey' => $column->primary_key ? 1 : 0,
							'autoincrement' => $column->auto_increment ? 1 : 0,
							'unique' => $column->unique ? 1 : 0,
							'null' => $column->null ? 1 : 0
						);
						
						$data['table']['columns'][] = $colArr;
					}

					// Build YAML
					$result = \Bedrock\Common\Data\YAML::encode($data);
					break;
					
				// CSV String
				case \Bedrock\Model::FORMAT_CSV:
					// Build Array
					$columns = array('class', 'name', 'type', 'length', 'size', 'default', 'comment', 'primarykey', 'autoincrement', 'unique', 'null', 'engine', 'charset', 'collation');

					// Table Properties
					$table = array(
						'class' => 'table',
						'name' => $this->_name
					);

					if($this->_properties['engine'] != '') {
						$table['engine'] = $this->_properties['engine'];
					}

					if($this->_properties['charset'] != '') {
						$table['charset'] = $this->_properties['charset'];
					}

					if($this->_properties['collation'] != '') {
						$table['collation'] = $this->_properties['collation'];
					}

					if($this->_properties['comment'] != '') {
						$table['comment'] = $this->_properties['comment'];
					}

					$data[] = $table;

					// Table Columns
					foreach($this->_columns as $column) {
						$data[] = array(
							'class' => 'column',
							'name' => $column->name,
							'type' => $column->type,
							'length' => $column->length,
							'size' => $column->size,
							'default' => $column->default,
							'comment' => $column->comment,
							'primarykey' => $column->primary_key ? 1 : 0,
							'autoincrement' => $column->auto_increment ? 1 : 0,
							'unique' => $column->unique ? 1 : 0,
							'null' => $column->null ? 1 : 0
						);
					}

					$result = \Bedrock\Common\Data\CSV::encode($data, ', ', $columns);
					break;
			}
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\DOMException $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('A problem was encountered converting the schema for table "' . $this->_name . '" to a string.');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('A problem was encountered converting the schema for table "' . $this->_name . '" to a string.');
		}
	}
	
	/**
	 * Returns the current table's data as a string of the specified type.
	 *
	 * @param integer $formatType the format type to use
	 * @return string the data represented as a string
	 */
	public function dataToString($formatType = \Bedrock\Model::FORMAT_SQL) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			
			// Check for Unsaved Changes
			if($this->_state != self::STATE_UNCHANGED) {
				throw new \Bedrock\Model\Exception('Table \'' . $this->_name . '\' data retrieval aborted, the table has unsaved changes.');
			}
			
			// Query Database
			$res = $this->_connection->query('SELECT * FROM ' . self::sanitize($this->_name));
			$rows = $res->fetchAll(\PDO::FETCH_ASSOC);
			
			switch($formatType) {
				default:
					
				// SQL Queries
				case \Bedrock\Model::FORMAT_SQL:
					foreach($rows as $row) {
						// Build SQL Query
						$insertQuery = 'INSERT INTO ' . self::sanitize($this->_name) . ' (';
						
						foreach($this->_columns as $column) {
							$insertQuery .= $column->name . ', ';
						}
						
						$insertQuery = substr($insertQuery, 0, strlen($insertQuery) - 2) . ') VALUES (';
						
						foreach($this->_columns as $column) {
							if($column->type == \Bedrock\Model\Column::FIELD_TYPE_INT ||
									$column->type == \Bedrock\Model\Column::FIELD_TYPE_FLOAT ||
									$column->type == \Bedrock\Model\Column::FIELD_TYPE_DOUBLE ||
									$column->type == \Bedrock\Model\Column::FIELD_TYPE_DECIMAL ||
									$column->type == \Bedrock\Model\Column::FIELD_TYPE_BOOL) {
								$insertQuery .= $row[$column->name] . ', ';
							}
							else {
								$insertQuery .= '\'' . $row[$column->name] . '\', ';
							}
						}
						
						$insertQuery = substr($insertQuery, 0, strlen($insertQuery) -2) . ');';
						
						$result .= $insertQuery . "\n";
					}
					
					break;
					
				// XML Document
				case \Bedrock\Model::FORMAT_XML:
					$xml = new \SimpleXMLElement('<records></records>');
					
					foreach($rows as $row) {
						$rowXml = $xml->addChild('record');
						
						foreach($this->_columns as $column) {
							$rowXml->addChild($column->name, $row[$column->name]);
						}
					}
					
					$xml = dom_import_simplexml($xml)->ownerDocument;
					$xml->formatOutput = true;
					$result = $xml->saveXML();
					
					break;
					
				// YAML Document
				case \Bedrock\Model::FORMAT_YAML:
					// Build Data
					$data = array('records' => array());

					// Add Records
					foreach($rows as $row) {
						$currentRecord = array();

						foreach($this->_columns as $column) {
							$currentRecord[$column->name] = $row[$column->name];
						}

						$data['records'][] = $currentRecord;
					}
					
					$result = \Bedrock\Common\Data\YAML::encode($data);
					break;
					
				// CSV Document
				case \Bedrock\Model::FORMAT_CSV:
					// Build Data
					$columns = array();
					$records = array();

					// Get Columns
					foreach($this->_columns as $column) {
						$columns[] = $column->name;
					}

					// Get Data
					foreach($rows as $row) {
						$currentRecord = array();

						foreach($columns as $column) {
							$currentRecord[$column] = $row[$column];
						}

						$records[] = $currentRecord;
					}
					
					$result = \Bedrock\Common\Data\CSV::encode($records, ', ', $columns);
					break;
			}
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('A problem was encountered converting the data for table "' . $this->_name . '" to a string.');
		}
	}
	
	/**
	 * Imports the table schema using the specified source. The table will not
	 * be created until the \Bedrock\Model\Table::save() method is called.
	 *
	 * @param string $importSource the source file to import from
	 * @param integer $importType the format of the source file
	 * @return boolean whether or not the import was successful
	 */
	public function importSchema($importSource, $importType = \Bedrock\Model::FORMAT_SQL, $sourceIsFile = true) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = false;

			if($sourceIsFile && !is_file($importSource)) {
				throw new \Bedrock\Model\Exception('The import source specified is invalid: "' . $importSource . '"');
			}

			// Copy/Backup Table
			$backupName = '_bed_' . \Bedrock\Common\String::random(4) . '_' . $this->_name;
			\Bedrock\Common\Logger::info('Creating backup of table "' . $this->_name . '" to "' . $backupName . '"');
			$sql = 'CREATE TABLE ' . self::sanitize($backupName) . ' SELECT * FROM ' . self::sanitize($this->_name);
			
			if(!$this->_connection->query($sql)) throw new \Bedrock\Model\Exception('Table backup failed for table "' . $this->_name . '".');

			// Drop Table
			if($this->drop()) throw new \Bedrock\Model\Exception('Dropping table "' . $this->_name . '" failed.');

			// Parse Data
			switch($importType) {
				default:

				// SQL Query
				case \Bedrock\Model::FORMAT_SQL:
					if($sourceIsFile) {
						$sql = file_get_contents($importSource);
					}
					else {
						$sql = $importSource;
					}
					break;

				// XML Document
				case \Bedrock\Model::FORMAT_XML:
					if($sourceIsFile) {
						$xml = simplexml_load_file($importSource);
					}
					else {
						$xml = simplexml_load_string($importSource);
					}

					$sql = 'CREATE TABLE `' . self::sanitize($xml['name']) . '` (';

					foreach($xml->columns->column as $column) {
						$colObj = new \Bedrock\Model\Column(array(
							'name' => $column['name'],
							'type' => $column->type,
							'length' => $column->length,
							'size' => $column->size,
							'null' => $column->flags->null == 0 ? false : true,
							'default' => $column->default,
							'primary_key' => $column->flags->primarykey,
							'foreign_key' => '',
							'foreign_key_type' => ''
						));

						$sql .= $colObj->definition . ', ';
					}

					$sql = substr($sql, 0, strlen($sql)-2) . ')';

					if(isset($xml->properties->engine)) {
						$sql .= ' ENGINE = ' . self::sanitize($xml->properties->engine);
					}

					if(isset($xml->properties->charset)) {
						$sql .= ' DEFAULT CHARACTER SET = ' . self::sanitize($xml->properties->charset);
					}

					if(isset($xml->properties->collation)) {
						$sql .= ' DEFAULT COLLATE = ' . self::sanitize($xml->properties->collation);
					}

					if(isset($xml->properties->comment)) {
						$sql .= ' COMMENT = \'' . self::sanitize($xml->properties->comment) . '\'';
					}

					break;

				// YAML Document
				case \Bedrock\Model::FORMAT_YAML:
					if($sourceIsFile) {
						$importString = file_get_contents($importSource);
					}
					else {
						$importString = $importSource;
					}

					$yaml = new \Bedrock\Common\Data\YAML($importString, true);
					$sql = 'CREATE TABLE `' . self::sanitize($yaml->table->name) . '` (';
					
					foreach($yaml->table->columns as $column) {
						$colObj = new \Bedrock\Model\Column(array(
							'name' => $column->name,
							'type' => $column->type,
							'length' => $column->length,
							'size' => $column->size,
							'null' => $column->flags->null,
							'default' => $column->default,
							'primary_key' => $column->flags->primarykey,
							'foreign_key' => '',
							'foreign_key_type' => ''
						));
						
						$sql .= $colObj->definition . ', ';
					}

					$sql = substr($sql, 0, strlen($sql)-2) . ')';

					if(isset($yaml->table->properties->engine)) {
						$sql .= ' ENGINE = ' . self::sanitize($yaml->table->properties->engine);
					}

					if(isset($yaml->table->properties->charset)) {
						$sql .= ' DEFAULT CHARACTER SET = ' . self::sanitize($yaml->table->properties->charset);
					}

					if(isset($yaml->table->properties->collation)) {
						$sql .= ' DEFAULT COLLATE = ' . self::sanitize($yaml->table->properties->collation);
					}

					if(isset($yaml->table->properties->comment)) {
						$sql .= ' COMMENT = \'' . self::sanitize($yaml->table->properties->comment) . '\'';
					}
					
					break;

				// CSV Document
				case \Bedrock\Model::FORMAT_CSV:
					if($sourceIsFile) {
						$importString = file_get_contents($importSource);
					}
					else {
						$importString = $importSource;
					}

					$csv = new \Bedrock\Common\Data\CSV($importString, ', ', true);
					$tableRow = null;
					$columnRows = array();
					
					foreach($csv as $row) {
						if($row['class'] == 'table') {
							$tableRow = $row;
						}
						elseif($row['class'] == 'column') {
							$columnRows[] = $row;
						}
					}
					
					$sql = 'CREATE TABLE `' . self::sanitize($tableRow['name']) . '` (';
					
					foreach($columnRows as $columnRow) {
						$colObj = new \Bedrock\Model\Column(array(
							'name' => isset($columnRow['name']) ? $columnRow['name'] : '',
							'type' => isset($columnRow['type']) ? $columnRow['type'] : '',
							'length' => isset($columnRow['length']) ? $columnRow['length'] : '',
							'size' => isset($columnRow['size']) ? $columnRow['size'] : '',
							'null' => isset($columnRow['null']) ? $columnRow['null'] : '',
							'default' => isset($columnRow['default']) ? $columnRow['default'] : '',
							'primary_key' => isset($columnRow['primarykey']) ? $columnRow['primaryKey'] : '',
							'foreign_key' => '',
							'foreign_key_type' => ''
						));

						$sql .= $colObj->definition . ', ';
					}

					$sql = substr($sql, 0, strlen($sql)-2) . ')';

					if(isset($tableRow['engine']) && $tableRow['engine'] != '') {
						$sql .= ' ENGINE = ' . self::sanitize($tableRow['engine']);
					}

					if(isset($tableRow['charset']) && $tableRow['charset'] != '') {
						$sql .= ' DEFAULT CHARACTER SET = ' . self::sanitize($tableRow['charset']);
					}

					if(isset($tableRow['collation']) && $tableRow['collation'] != '') {
						$sql .= ' DEFAULT COLLATE = ' . self::sanitize($tableRow['collation']);
					}

					if(isset($tableRow['comment']) && $tableRow['comment'] != '') {
						$sql .= ' COMMENT = \'' . self::sanitize($tableRow['comment']) . '\'';
					}

					break;
			}

			// Execute Queries
			\Bedrock\Common\Logger::info('Importing schema with query: ' . $sql);
			$res = $this->_connection->query($sql);

			if(!$res) {
				$this->revert($backupName);
				$result = false;
			}
			else {
				\Bedrock\Common\Logger::info('Removing backup table "' . $backupName .'"...');
				$this->_connection->query('DROP TABLE IF EXISTS ' . self::sanitize($backupName));
				$result = true;
			}
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Bedrock\Model\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();

			if(isset($backupName)) {
				$this->revert($backupName);
			}

			throw new \Bedrock\Model\Exception('Schema import failed.');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();

			if(isset($backupName)) {
				$this->revert($backupName);
			}

			throw new \Bedrock\Model\Exception('Schema import failed.');
		}
	}
	
	/**
	 * Exports the table schema to the specified location using the specified
	 * format.
	 *
	 * @param string $exportLocation the file to which the exported schema will be saved
	 * @param integer $exportType the export format to use
	 */
	public function exportSchema($exportLocation, $exportType = \Bedrock\Model::FORMAT_SQL) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			\Bedrock\Common\Logger::info('Exporting schema of table "' . $this->_name . '" as ' . strtoupper(self::formatToString($exportType)) . ' to "' . $exportLocation . '" ...');
			$fileContents = $this->schemaToString($exportType);
			self::writeFile($exportLocation, $fileContents);
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('Schema export failed.');
		}
	}
	
	/**
	 * Imports table data from the specified source. The contents of the source
	 * file must match the table's current schema.
	 *
	 * @param string $importSource the source file to import from
	 * @param integer $importType the format of the source file
	 * @param boolean $append whether or not existing data should be replaced or appended to
	 */
	public function importData($importSource, $importType = \Bedrock\Model::FORMAT_SQL, $append = false) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$importSql = array();
			
			switch($importType) {
				default:
				
				// SQL Query
				case \Bedrock\Model::FORMAT_SQL:
					$importSql = file($importSource);
					break;
					
				// XML Document
				case \Bedrock\Model::FORMAT_XML:
					$importXml = new \SimpleXMLElement($importSource, NULL, true);
					
					foreach($importXml->record as $row) {
						$sql = 'INSERT INTO ' . $this->_name . '(';
						
						foreach($row->children() as $field) {
							$sql .= $field->getName() . ', ';
						}
						
						$sql = substr($sql, 0, strlen($sql) - 2) . ') VALUES (';
						
						foreach($row->children() as $field) {
							$sql .= '\'' . $field . '\', ';
						}
						
						$sql = substr($sql, 0, strlen($sql) -2) . ');';
						$importSql[] = $sql;
					}
					break;
					
				// YAML Document
				case \Bedrock\Model::FORMAT_YAML:
					$importYaml = new \Bedrock\Common\Data\YAML(file_get_contents($importSource));
					
					foreach($importYaml->records as $record) {
						$sql = 'INSERT INTO ' . $this->_name . '(';

						foreach($columns as $key => $column) {
							if($key == count($columns) - 1) {
								$column = substr($column, 0, strlen($column) - 1);
							}

							$sql .= $column . ', ';
						}

						$sql = substr($sql, 0, strlen($sql) - 2) . ') VALUES (';

						foreach($columns as $key => $column) {
							if($key == count($columns) - 1) {
								$column = substr($column, 0, strlen($column) - 1);
							}

							$sql .= '\'' . $record->$column . '\', ';
						}

						$sql = substr($sql, 0, strlen($sql) - 2) . ');';
						$importSql[] = $sql;
					}

					break;
					
				// CSV Document
				case \Bedrock\Model::FORMAT_CSV:
					$importCsv = file($importSource);
					$columns = explode(',', array_shift($importCsv));
					$values = array();
					
					foreach($importCsv as $csv) {
						$values = explode(',', $csv);
						$sql = 'INSERT INTO ' . $this->_name . '(';
						
						foreach($columns as $key => $column) {
							if($key == count($columns) - 1) {
								$column = substr($column, 0, strlen($column) - 1);
							}
							
							$sql .= $column . ', ';
						}
						
						$sql = substr($sql, 0, strlen($sql) - 2) . ') VALUES (';
						
						foreach($values as $key => $value) {
							if($key == count($values) - 1) {
								$value = substr($value, 0, strlen($value) - 1);
							}
							
							$sql .= '\'' . $value . '\', ';
						}
						
						$sql = substr($sql, 0, strlen($sql) - 2) . ');';
						$importSql[] = $sql;
					}
					
					break;
			}
			
			if(!$append) {
				$this->_connection->exec('DELETE FROM ' . $this->_name);
			}
			
			foreach($importSql as $query) {
				$this->_connection->exec($query);
			}
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('Data import failed.');
		}
	}
	
	/**
	 * Exports the table's data to the specified location using the specified
	 * format.
	 *
	 * @param string $exportLocation the file to which exported data will be saved
	 * @param integer $exportType the export format to use
	 */
	public function exportData($exportLocation, $exportType = \Bedrock\Model::FORMAT_SQL) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			\Bedrock\Common\Logger::info('Exporting data in table "' . $this->_name . '" as ' . strtoupper(self::formatToString($exportType)) . '...');
			$fileContents = $this->dataToString($exportType);
			self::writeFile($exportLocation, $fileContents);
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Exception('Data export failed.');
		}
	}
}