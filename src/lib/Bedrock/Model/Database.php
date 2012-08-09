<?php
namespace Bedrock\Model;

/**
 * Represents a database connection, manages general database operations, and
 * stores properties describing the currently connected database.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 08/29/2008
 * @updated 07/02/2012
 */
class Database extends \Bedrock {
	const DB_TYPE_MYSQL = 0;
	const DB_TYPE_POSTGRES = 1;
	const DB_TYPE_MSSQL = 2;
	
	protected $_connection;
	protected $_name;
	protected $_type;
	protected $_tables;
	
	/**
	 * Initializes a database connection.
	 *
	 * @param mixed $databaseConfig the database configuration
	 */
	public function __construct($databaseConfig) {
		try {
			// Build Connection String
			$dsn = $databaseConfig->type . ':host=' . $databaseConfig->host . ';dbname=' . $databaseConfig->dbname;
			
			\Bedrock\Common\Logger::info('Connecting to database "' . $databaseConfig->dbname . '" on "' . $databaseConfig->host . '"...');
			
			// Set Properties
			switch($databaseConfig->type) {
				case 'mysql':
					$this->_type = self::DB_TYPE_MYSQL;
					break;
			}
			
			// Initialize Database Connection
			$this->_connection = new \PDO($dsn, $databaseConfig->username, $databaseConfig->password);
			$this->_name = $databaseConfig->dbname;
			$this->_dbConfig = $databaseConfig;
			
			parent::__construct();
		}
		catch(\PDOException $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Model\Exception('There was a problem retrieving the table information.');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Model\Exception('There was a problem connecting to the database.');
		}
	}
	
	/**
	 * Retrieves the requested table as a table object.
	 *
	 * @param string $tableName the name of the table to retrieve
	 * @return \Bedrock\Model\Table the resulting table object
	 */
	public function __get($tableName) {
		try {
			// If requested table isn't cached, load it.
			if(!is_object($this->_tables[$tableName])) {
				$this->_tables[$tableName] = new \Bedrock\Model\Table(array('name' => $tableName), $this);
				$this->_tables[$tableName]->load();
			}
			return $this->_tables[$tableName];
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Model\Exception('There was a problem retrieving the table information.');
		}
	}
	
	/**
	 * Loads all current database and table details into the database object.
	 */
	public function load() {
		try {
			// Get Table List
			$res = $this->_connection->query('SHOW TABLES');
			
			while($row = $res->fetch(\PDO::FETCH_NUM)) {
				$this->__get($row[0]);
			}
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Returns the current database connection.
	 *
	 * @return \PDO the current database connection
	 */
	public function getConnection() {
		if($this->_connection) {
			return $this->_connection;
		}
		else {
			return NULL;
		}
	}
	
	/**
	 * Returns all tables currently stored with the Database object.
	 *
	 * @return array an array of the corresponding Table objects
	 */
	public function getTables() {
		return $this->_tables;
	}
	
	/**
	 * Returns configuration details for the current database connection.
	 *
	 * @return array an array containing the configuration details
	 */
	public function getConfig() {
		return $this->_dbConfig;
	}

	/**
	 * Returns the current database's table definitions as a string using the
	 * default format.
	 *
	 * @return string all current table definitions
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Returns the current database's table definitions as a string using the
	 * default format.
	 *
	 * @param string $format the desired output format
	 * @return string all current table definitions
	 */
	public function toString($format = \Bedrock\Model::FORMAT_SQL) {
		try {
			// Setup
			$result = '';

			// Build String
			if(empty($this->_tables)) {
				$this->load();
			}
			
			foreach($this->_tables as $table) {
				$result .= $table->toString($format);
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}

	/**
	 * Imports table schemas into the current database (overwrites any existing
	 * tables and records).
	 *
	 * @param string $importSource the file containing the definitions to import
	 * @param string $importType the format used
	 * @param boolean $sourceIsFile whether or not the specified source is a file
	 * @return boolean whether or not the import was successful
	 */
	public function importTableSchemas($importSource, $importType = \Bedrock\Model::FORMAT_SQL, $sourceIsFile = true) {
		try {
			// Setup
			$result = true;

			\Bedrock\Common\Logger::info('Importing table schemas for database "' . $this->_name . '"...');
			$this->load();

			if(!is_file($importSource)) {
				throw new \Bedrock\Model\Exception('The import source specified is invalid: "' . $importSource . '"');
			}

			switch($importType) {
				default:

				case \Bedrock\Model::FORMAT_SQL:
					$lines = file($importSource);
					$updated = false;

					foreach($lines as $line) {
						$line = trim($line);

						if(preg_match("/^CREATE\s+(?:TEMPORARY\s+)?TABLE\s+(?:IF NOT EXISTS\s+)?([^\s]+)/i", $line, $matches)) {
							$tableName = trim($matches[1], '`');
							
							foreach($this->_tables as $key => $table) {
								if($table->getProperty('name') == $tableName) {
									$result &= $this->_tables[$key]->importSchema($line, \Bedrock\Model::FORMAT_SQL, false);
									$updated = true;
								}
							}

							if(!$updated) {
								$newTable = new \Bedrock\Model\Table();
								$result &= $this->_tables[$tableName] = $newTable->importSchema($line, \Bedrock\Model::FORMAT_SQL, false);
								$updated = false;
							}
						}
					}

					break;

				case \Bedrock\Model::FORMAT_XML:
					$xml = simplexml_load_file($importSource);
					$updated = false;
					
					foreach($xml as $xmlTable) {
						foreach($this->_tables as $key => $table) {
							if($table->getProperty('name') == $xmlTable['name']) {
								$result &= $this->_tables[$key]->importSchema($xmlTable->asXML(), \Bedrock\Model::FORMAT_XML, false);
								$updated = true;
							}
						}

						if(!$updated) {
							$newTable = new \Bedrock\Model\Table(array(), $this);
							$result &= $this->_tables[$xmlTable['name']] = $newTable->importSchema($xmlTable->asXML(), \Bedrock\Model::FORMAT_XML, false);
							$updated = false;
						}
					}

					break;

				case \Bedrock\Model::FORMAT_YAML:
					$importData = file_get_contents($importSource);
					$yaml = new \Bedrock\Common\Data\YAML($importData);
					$updated = false;

					foreach($yaml->tables as $yamlTable) {
						foreach($this->_tables as $key => $table) {
							if($table->getProperty('name') == $yamlTable['name']) {
								$yamlTable = new \Bedrock\Common\Data\YAML(array('table' => $yamlTable), true);
								$result &= $this->_tables[$key]->importSchema((string) $yamlTable, \Bedrock\Model::FORMAT_YAML, false);
								$updated = true;
							}
						}

						if(!$updated) {
							$newTable = new \Bedrock\Model\Table(array(), $this);
							$yamlTable = new \Bedrock\Common\Data\YAML(array('table' => $yamlTable), true);
							$result &= $this->_tables[$yamlTable['name']] = $newTable->importSchema((string) $yamlTable, \Bedrock\Model::FORMAT_YAML, false);
							$updated = false;
						}
					}

					break;

				case \Bedrock\Model::FORMAT_CSV:
					$importData = file_get_contents($importSource);
					$csv = new \Bedrock\Common\Data\CSV($importData, ', ', true);
					$updated = false;
					$arrayTable = array();
					$lastRowNum = count($csv) - 1;
					
					foreach($csv as $rowNum => $row) {
						if($row['class'] == 'table' || $rowNum == $lastRowNum) {
							if(count($arrayTable)) {
								foreach($this->_tables as $key => $table) {
									if($table->getProperty('name') == $arrayTable[0]['name']) {
										$csvTable = new \Bedrock\Common\Data\CSV($arrayTable, ', ', true);
										$result &= $this->_tables[$key]->importSchema((string) $csvTable, \Bedrock\Model::FORMAT_CSV, false);
										$updated = true;
										break;
									}
								}

								if(!$updated) {
									$newTable = new \Bedrock\Model\Table(array(), $this);
									$csvTable = new \Bedrock\Common\Data\CSV($arrayTable, ', ', true);
									$result &= $this->_tables[$arrayTable[0]['name']] = $newTable->importSchema((string) $csvTable, \Bedrock\Model::FORMAT_CSV, false);
									$updated = false;
								}
							}

							$arrayTable = array();
						}
						
						$arrayTable[] = $row;
					}
					
					break;
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}

	/**
	 * Exports all table schemas to the specified location.
	 *
	 * @param string $exportLocation the file to write
	 * @param string $exportType the format to use
	 */
	public function exportTableSchemas($exportLocation, $exportType = \Bedrock\Model::FORMAT_SQL) {
		try {
			// Setup
			$fileContents = '';
			
			\Bedrock\Common\Logger::info('Exporting table schemas for database "' . $this->_name . '"...');

			// Build String
			$this->load();

			switch($exportType) {
				case \Bedrock\Model::FORMAT_SQL:
					foreach($this->_tables as $table) {
						\Bedrock\Common\Logger::info('Exporting schema of table "' . $table->getProperty('name') . '"...');
						$fileContents .= $table->schemaToString($exportType) . ';' . \Bedrock\Common::TXT_NEWLINE;
					}
					break;

				case \Bedrock\Model::FORMAT_XML:
					foreach($this->_tables as $table) {
						$fileContents .= $table->schemaToString($exportType);
						$fileContents = str_replace('<?xml version="1.0"?>' . \Bedrock\Common::TXT_NEWLINE, '', $fileContents);
					}

					$fileContents = '<tables>' . $fileContents . '</tables>';
					$fileContents = \DOMDocument::loadXML($fileContents);
					$fileContents->formatOutput = true;
					$fileContents->preserveWhitespace = false;
					$fileContents = $fileContents->saveXML();
					break;

				case \Bedrock\Model::FORMAT_YAML:
					$yaml = new \Bedrock\Common\Data\YAML();
					$tables = array();

					foreach($this->_tables as $table) {
						$table = new \Bedrock\Common\Data\YAML($table->schemaToString($exportType));
						$tables[] = $table->table;
					}

					$yaml->tables = $tables;

					$fileContents = (string) $yaml;
					break;

				case \Bedrock\Model::FORMAT_CSV:
					$first = true;

					foreach($this->_tables as $table) {
						$tableCsv = $table->schemaToString($exportType);

						if(!$first) {
							$tableCsv = substr($tableCsv, strpos($tableCsv, \Bedrock\Common::TXT_NEWLINE) + 1);
						}

						$fileContents .= $tableCsv;
						$first = false;
					}
					break;
			}
			
			\Bedrock\Model::writeFile($exportLocation, $fileContents);
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Model\Exception('Table schema export failed.');
		}
	}
	
	/**
	 * Imports all table data from the specified source directory.
	 *
	 * @param string $importSource the source directory containing the files to import
	 * @param integer $importType the type of data the source files use
	 * @param boolean $append whether or not to append the imported data to existing table data
	 */
	public function importTableData($importSource, $importType, $append = false) {
		try {
			// Setup
			$ext = '.' . $importType;
			$fileName = '';
			
			// Load Tables
			$this->load();
			
			// Import Data
			\Bedrock\Common\Logger::info('Attempting to import table data for database "' . $this->_name . '" from location "' . $importSource . '" ...');
			
			foreach($this->_tables as $name => $table) {
				$fileName = $importSource . $name . $ext;
				
				if(is_file($fileName)) {
					\Bedrock\Common\Logger::info('File "' . $fileName . '" found, importing for corresponding table...');
					$table->importData($fileName, $importType, $append);
				}
			}
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Exports all table data from the database to the specified location.
	 *
	 * @param integer $exportType the data type to use
	 * @param string $exportLocation the location to which to export
	 */
	public function exportTableData($exportLocation, $exportType) {
		try {
			// Load Tables
			$this->load();
			
			\Bedrock\Common\Logger::info('Exporting all table data for database "' . $this->_name . '" to location "' . $exportLocation . '" ...');
			
			// Export Data
			foreach($this->_tables as $name => $table) {
				\Bedrock\Common\Logger::info('Exporting table "' . $name . '" ...');
				$table->exportData($exportLocation, $exportType);
			}
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
}