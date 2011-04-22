<?php
/**
 * A query object used to manage transactions and queries executed against the
 * database.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 08/29/2008
 * @updated 08/29/2008
 */
class Bedrock_Model_Query extends Bedrock_Model {
	const TARGET_TABLE = 0;
	const TARGET_VIEW = 1;
	const TARGET_PROCEDURE = 2;
	
	protected static $_instance;
	protected $_target;
	protected $_table;
	protected $_view;
	protected $_procedure;
	protected $_query;
	
	/**
	 * Used to initialize a query. See the static from() method for public
	 * initialization.
	 *
	 * @param string $targetName the name of the table/view/procedure to be queried
	 * @param integer $targetType the type of target being queried
	 * @param PDO $database an optional database connection to use, the default will be used otherwise
	 */
	public function __construct($targetName = '', $targetType = self::TARGET_TABLE, $database = NULL) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			parent::__construct($database);
			
			switch($targetType) {
				default:
				case self::TARGET_TABLE:
				case self::TARGET_VIEW:
					$this->_target = self::TARGET_TABLE;
					$this->_table = $targetName;
					$this->_query['from'] = 'SELECT * FROM ' . self::sanitize($targetName);
					break;
					
				case self::TARGET_PROCEDURE:
					$this->_target = self::TARGET_PROCEDURE;
					$this->_procedure = $targetName;
					break;
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('The query could not be initialized for table "' . $table . '"');
		}
	}
	
	/**
	 * Resets the query object and clears any existing queries.
	 */
	protected function reset() {
		$this->_query = array();
	}
	
	/**
	 * Initializes a query using the specified table.
	 *
	 * @param string $table the name of the table to be queried
	 * @return Bedrock_Model_Query a new query object
	 */
	public static function from($table) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			Bedrock_Common_Logger::info('Querying from table "' . $table . '"');
			self::$_instance = new self($table, self::TARGET_TABLE);
			
			Bedrock_Common_Logger::logExit();
			return self::$_instance;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('The query could not be initialized for table "' . $table . '"');
		}
	}
	
	/**
	 * Initializes a query using the specified stored procedure.
	 *
	 * @param string $procedure the name of the procedure to be run
	 * @return Bedrock_Model_Query a new query object
	 */
	public function procedure($procedure) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			Bedrock_Common_Logger::info('Querying from procedure "' . $procedure . '"');
			self::$_instance = new self($procedure, self::TARGET_PROCEDURE);
			
			Bedrock_Common_Logger::logExit();
			return self::$_instance;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Specifies a WHERE clause for the current query.
	 *
	 * @param string $field the field to compare, or the stored procedure parameter name
	 * @param string $operator the operator to use (=, >, <, <>, etc.)
	 * @param string $value one or more value parameters
	 * @return Bedrock_Model_Query the updated Query object
	 */
	public function where($field, $operator, $value) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$logValue = $value;
			
			switch(gettype($value)) {
				case 'array':
					$logValue = 'Array(' . implode(', ', $value);
					$logValue = substr($logValue, 0, strlen($logValue)-2) . ')';
					break;
					
				case 'boolean':
					$logValue = $logValue ? 'true' : 'false';
					break;
					
				case 'string':
					$logValue = '\'' . $value . '\'';
					break;
					
				default:
					// Do Nothing
					break;
			}
			
			switch($this->_target) {
				default:
				case self::TARGET_TABLE:
				case self::TARGET_VIEW:
					if($this->_query['associate']) {
						Bedrock_Common_Logger::info('Applying WHERE clause to record association: ' . $field . ' ' . $operator . ' ' . $logValue);
						
					}
					else {
						Bedrock_Common_Logger::info('Adding to WHERE clause: ' . $field . ' ' . $operator . ' ' . $logValue);
						$this->_query['where'][] = array('field' => $field, 'operator' => $operator, 'value' => $value);
					}
					break;
					
				case self::TARGET_PROCEDURE:
					Bedrock_Common_Logger::info('Assigning value "' . $logValue . '" to parameter "' . $field . '"');
					$this->_query['params'][$field] = $value;
					break;
			}
			
			Bedrock_Common_Logger::logExit();
			return $this;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('A where clause could not be added to the Query object.');
		}
	}
	
	/**
	 * Specifies sort parameters to use for the current query.
	 *
	 * @param string $sortParams any sort parameters to apply
	 * @return Bedrock_Model_Query the updated Query object
	 */
	public function sort($sortParams) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($this->_target == self::TARGET_PROCEDURE) {
				throw new Bedrock_Model_Query_Exception('ORDER BY clauses cannot be used on stored procedure queries.');
			}
			
			Bedrock_Common_Logger::info('Applying sorting parameters: "' . $sortParams . '"');
			$this->_query['sort'][] = $sortParams;
			
			Bedrock_Common_Logger::logExit();
			return $this;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('The specified sort parameters could not be applied to the Query object.');
		}
	}
	
	/**
	 * Specifies a limit for the number of results returned, and what record
	 * number to start with.
	 *
	 * @param integer $start the record number to start with
	 * @param integer $count the total number of records to return
	 * @return Bedrock_Model_Query the updated Query object
	 */
	public function limit($start, $count) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($this->_target == self::TARGET_PROCEDURE) {
				throw new Bedrock_Model_Query_Exception('LIMIT clauses cannot be used on stored procedure queries.');
			}
			
			Bedrock_Common_Logger::info('Applying start limit: ' . $start);
			Bedrock_Common_Logger::info('Applying count limit: ' . $count);
			$this->_query['limit'] = ' LIMIT ' . self::sanitize($start) . ', ' . self::sanitize($count);
			
			Bedrock_Common_Logger::logExit();
			return $this;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('The specified limit could not be applied to the Query object.');
		}
	}
	
	/**
	 * Sets the query to retrieve all records that match the supplied Record
	 * object.
	 *
	 * @param Bedrock_Model_Record $record the record object against which to perform a match
	 * @return Bedrock_Model_Query the updated Query object
	 */
	public static function match($record) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($this->_target == self::TARGET_PROCEDURE) {
				throw new Bedrock_Model_Query_Exception('MATCH clauses cannot be used on stored procedure queries.');
			}
			
			// Build Where Clause
			$where = '';
			
			foreach($record as $column => $value) {
				if($value) {
					$where .= $column . '=\'' . $value . '\' AND ';
				}
			}
			
			$where = substr($where, 0, strlen($where)-5);
			
			self::$_instance = new self($record->getProperty('table'));
			self::$_instance->where($where);
			
			Bedrock_Common_Logger::logExit();
			return  self::$_instance;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('The specified record could not be used for matching.');
		}
	}
	
	/**
	 * Associates a record from one table with one or more records from another.
	 *
	 * @param Bedrock_Model_Record $firstRecord the record to associate
	 * @param Bedrock_Model_Record $secondRecord the second record to associate, or null to use a where clause
	 * @return Bedrock_Model_Query the resulting query object when the second record is null, allowing for a where clause
	 */
	public function associate($firstRecord, $secondRecord = NULL) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($this->_target == self::TARGET_PROCEDURE) {
				throw new Bedrock_Model_Query_Exception('Stored procedure queries cannot be associated with other records.');
			}
			
			if(is_null($secondRecord)) {
				$this->reset();
				$this->_query['associate'] = $firstRecord;
				Bedrock_Common_Logger::logExit();
				return $this;
			}
			else {
				// Retrieve Corresponding Tables
				$firstTable = $firstRecord->getTable();
				$secondTable = $secondRecord->getTable();
				
				// Verify Table Mappings
				$firstMappings = $firstTable->getMappings();
				
				if(array_key_exists($secondTable->getProperty('name'), $firstMappings)) {
					$firstMapping = $firstMappings[$secondTable->getProperty('name')];
					
					switch($firstMapping) {	
						default:
						case Bedrock_Model_Table::MAP_TYPE_ONE_ONE:
						case Bedrock_Model_Table::MAP_TYPE_ONE_MANY:
							// Update First Record's Table
							$firstForeignKeys = $firstTable->getForeignKeys();
							$foreignKey = $firstForeignKeys[$secondTable->getProperty('name')]->name;
							
							$sql = 'UPDATE ' . $firstTable->getProperty('name') . ' SET ' . $foreignKey . ' = ' . self::sanitize($secondRecord->{$secondRecord->getPrimaryKey()}) . ' WHERE ' . $firstRecord->getPrimaryKey() . ' = ' . self::sanitize($firstRecord->{$firstRecord->getPrimaryKey()});
							Bedrock_Common_Logger::info('Associating: "' . $sql . '"');
							$this->_connection->exec($sql);
							break;
							
						case Bedrock_Model_Table::MAP_TYPE_MANY_ONE:
							// Update Second Record's Table
							$secondForeignKeys = $secondTable->getForeignKeys();
							$foreignKey = $secondForeignKeys[$secondTable->getProperty('name')]->name;
							
							$sql = 'UPDATE ' . $secondTable->getProperty('name') . ' SET ' . $foreignKey . ' = ' . self::sanitize($firstRecord->{$firstRecord->getPrimaryKey()}) . ' WHERE ' . $secondRecord->getPrimaryKey() . ' = ' . self::sanitize($secondRecord->{$secondRecord->getPrimaryKey()});
							Bedrock_Common_Logger::info('Associating: "' . $sql . '"');
							$this->_connection->exec($sql);
							break;
							
						case Bedrock_Model_Table::MAP_TYPE_MANY_MANY:
							// Use Mapping Table
							$mapTableName = Bedrock_Model_Utilities::getMappingTableName($firstTable->getProperty('name'), $secondTable->getProperty('name'));
							
							$firstField = $firstRecord->getPrimaryKey() . '_' . $firstTable->getProperty('name');
							$secondField = $secondRecord->getPrimaryKey() . '_' . $secondTable->getProperty('name');
							
							$firstPrimary = $firstRecord->{$firstRecord->getPrimaryKey()};
							$secondPrimary = $secondRecord->{$secondRecord->getPrimaryKey()};
							
							// Check for Existing Association
							$countSql = 'SELECT COUNT(*) AS count FROM ' . $mapTableName . ' WHERE ' . $firstField . ' = ' . $firstPrimary . ' AND ' . $secondField . ' = ' . $secondPrimary;
							Bedrock_Common_Logger::info('Checking for existing association: "' . $countSql . '"');
							$countResult = $this->_connection->query($countSql)->fetch(PDO::FETCH_ASSOC);
							
							if($countResult['count'] == 0) {
								// Add Association
								$sql = 'INSERT INTO ' . $mapTableName . ' (' . $firstField . ', ' . $secondField . ') ' .
										'VALUES (' . $firstPrimary . ', ' . $secondPrimary . ')';
								
								Bedrock_Common_Logger::info('No association found, adding: "' . $sql . '"');
								
								$this->_connection->exec($sql);
							}
							else {
								Bedrock_Common_Logger::info('Association exists, no further action is necessary.');
							}
							
							break;
					}
				}
				else {
					throw new Bedrock_Model_Query_Exception('Records could not be associated, no compatible table mappings were found.');
				}
				
				Bedrock_Common_Logger::logExit();
				return;
			}
		}
		catch(PDOException $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('A problem with the database connection was encountered.');
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('A general error occurred.');
		}
	}
	
	/**
	 * Removes associations between a record from one table with one or more
	 * records from another.
	 *
	 * @param Bedrock_Model_Record $firstRecord the record to dissociate
	 * @param Bedrock_Model_Record $secondRecord the second record to dissociate, or null to use a where clause
	 * @return Bedrock_Model_Query the resulting query object when the second record is null, allowing for a where clause
	 */
	public function dissociate($firstRecord, $secondRecord = NULL) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($this->_target == self::TARGET_PROCEDURE) {
				throw new Bedrock_Model_Query_Exception('Stored procedure queries cannot be associated with other records.');
			}
			
			if(is_null($secondRecord)) {
				$this->reset();
				$this->_query['dissociate'] = $firstRecord;
				Bedrock_Common_Logger::logExit();
				return $this;
			}
			else {
				// Retrieve Corresponding Tables
				$firstTable = $firstRecord->getTable();
				$secondTable = $secondRecord->getTable();
				
				// Verify Table Mappings
				$firstMappings = $firstTable->getMappings();
				
				if(array_key_exists($secondTable->getProperty('name'), $firstMappings)) {
					$firstMapping = $firstMappings[$secondTable->getProperty('name')];
					
					switch($firstMapping) {
						default:
						case Bedrock_Model_Table::MAP_TYPE_ONE_ONE:
						case Bedrock_Model_Table::MAP_TYPE_ONE_MANY:
							// Update First Record's Table
							$firstForeignKeys = $firstTable->getForeignKeys();
							$foreignKey = $firstForeignKeys[$secondTable->getProperty('name')]->name;
							
							$sql = 'UPDATE ' . $firstTable->getProperty('name') . ' SET ' . $foreignKey . ' = NULL WHERE ' . $firstRecord->getPrimaryKey() . ' = ' . self::sanitize($firstRecord->{$firstRecord->getPrimaryKey()});
							Bedrock_Common_Logger::info('Dissociating: "' . $sql . '"');
							$this->_connection->exec($sql);
							break;
							
						case Bedrock_Model_Table::MAP_TYPE_MANY_ONE:
							// Update Second Record's Table
							$secondForeignKeys = $secondTable->getForeignKeys();
							$foreignKey = $secondForeignKeys[$secondTable->getProperty('name')]->name;
							
							$sql = 'UPDATE ' . $secondTable->getProperty('name') . ' SET ' . $foreignKey . ' = NULL WHERE ' . $secondRecord->getPrimaryKey() . ' = ' . self::sanitize($secondRecord->{$secondRecord->getPrimaryKey()});
							Bedrock_Common_Logger::info('Dissociating: "' . $sql . '"');
							$this->_connection->exec($sql);
							break;
							
						case Bedrock_Model_Table::MAP_TYPE_MANY_MANY:
							// Use Mapping Table
							$mapTableName = Bedrock_Model_Utilities::getMappingTableName($firstTable->getProperty('name'), $secondTable->getProperty('name'));
							
							$firstField = $firstRecord->getPrimaryKey() . '_' . $firstTable->getProperty('name');
							$secondField = $secondRecord->getPrimaryKey() . '_' . $secondTable->getProperty('name');
							
							$firstPrimary = $firstRecord->{$firstRecord->getPrimaryKey()};
							$secondPrimary = $secondRecord->{$secondRecord->getPrimaryKey()};
							
							// Remove Association
							$sql = 'DELETE FROM ' . $mapTableName . ' WHERE ' .
									$firstField . ' = ' . self::sanitize($firstPrimary) . ' AND ' .
									$secondField . ' = ' . self::sanitize($secondPrimary);
							
							Bedrock_Common_Logger::info('Dissociating Records: "' . $sql . '"');
							$this->_connection->exec($sql);
							break;
					}
				}
				else {
					throw new Bedrock_Model_Query_Exception('Records could not be dissociated, no compatible table mappings were found.');
				}
				
				Bedrock_Common_Logger::logExit();
				return;
			}
		}
		catch(PDOException $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('A problem with the database connection was encountered.');
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('The specified Record(s) could not be dissociated.');
		}
	}
	
	/**
	 * Queries for all records from the target table that are associated with
	 * the specified record.
	 *
	 * @param Bedrock_Model_Record $record the record to find associations with
	 * @param string $targetTableName the name of the target table from which to retrieve associated Records
	 */
	public static function associated($record, $targetTableName, $limit = array()) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($this->_target == self::TARGET_PROCEDURE) {
				throw new Bedrock_Model_Query_Exception('Stored procedure queries cannot have associated records.');
			}
			
			// Setup
			$result = new Bedrock_Model_ResultSet();
			$connection = Bedrock_Common_Registry::get('database')->getConnection();
			$recordTable = $record->getTable();
			$recordTableName = $recordTable->getProperty('name');
			$targetTable = new Bedrock_Model_Table(array('name' => $targetTableName));
			
			$targetTable->load();
			
			// Verify Table Association
			$mappings = $recordTable->getMappings();
			
			if(array_key_exists($targetTableName, $mappings)) {
				// Query for Associated Records
				switch($mappings[$targetTableName]) {
					default:
					case Bedrock_Model_Table::MAP_TYPE_ONE_ONE:
					case Bedrock_Model_Table::MAP_TYPE_ONE_MANY:
						$foreignKeys = $targetTable->getForeignKeys();
						$query = Bedrock_Model_Query::from($targetTableName)->where($foreignKeys[$recordTableName]->name, '=', $record->{$record->getPrimaryKey()});
						
						if(count($limit)) {
							$query = $query->limit($limit['start'], $limit['count']);
						}
						
						$result = $query->execute();
						
						break;
						
					case Bedrock_Model_Table::MAP_TYPE_MANY_ONE:
						$foreignKeys = $recordTable->getForeignKeys();
						$query = Bedrock_Model_Query::from($targetTableName)->where($targetTable->getPrimaryKey()->name, '=', $record->{$foreignKeys[$targetTableName]->name});
						
						if(count($limit)) {
							$query = $query->limit($limit['start'], $limit['count']);
						}
						
						$result = $query->execute();
						
						break;
						
					case Bedrock_Model_Table::MAP_TYPE_MANY_MANY:
						$mappingTableName = Bedrock_Model_Utilities::getMappingTableName($recordTableName, $targetTableName);
						$query = 'SELECT t.* FROM ' . $targetTableName . ' t LEFT JOIN ' . $mappingTableName . ' m ON t.' . $targetTable->getPrimaryKey()->name . ' = m.' . $targetTable->getPrimaryKey()->name . '_' . $targetTableName . ' WHERE m.' . $recordTable->getPrimaryKey()->name . '_' . $recordTableName . ' = :recordPrimaryKey';
						
						if(count($limit)) {
							$query .= ' LIMIT ' . $limit['start'] . ', ' . $limit['count'];
						}
						
						$statement = $connection->prepare($query);
						$statement->execute(array(':recordPrimaryKey' => $record->{$record->getPrimaryKey()}));
						$results = $statement->fetchAll(PDO::FETCH_ASSOC);
						
						if($results) {
							foreach($results as $result) {
								$records[] = new Bedrock_Model_Record($targetTableName, $result);
							}
						}
						
						$result = new Bedrock_Model_ResultSet($records);
						$result->setCountAll(count($records));
						break;
				}
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(PDOException $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('There was a problem with the database connection, associated records could not be retrieved.');
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('Could not retrieve associated records for the specified Record object.');
		}
	}
	
	/**
	 * Executes the assembled query and returns the corresponding results.
	 * 
	 * @return Bedrock_Model_ResultSet a ResultSet object holding the corresponding results
	 */
	public function execute() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$sql = '';
			$records = array();
			$results = array();
			
			// Build Query
			switch($this->_target) {
				default:
				case self::TARGET_TABLE:
				case self::TARGET_VIEW:
					if($this->_table) {
						$sql = $this->_query['from'];
						$whereClause = '';
						
						if(is_array($this->_query['where'])) {
							foreach($this->_query['where'] as $key => $where) {
								if($key == 0) {
									$whereClause .= ' WHERE ' . self::sanitize($where['field']);
								}
								else {
									$whereClause .= ' AND ' . self::sanitize($where['field']);
								}
								
								if(is_array($where['value'])) {
									// Array of Values
									switch($where['operator']) {
										case '=':
											$whereClause .= ' IN (' . self::valueToString($where['value']) . ')';
											break;
											
										case '<>':
										case '!=':
											$whereClause .= ' NOT IN (' . self::valueToString($where['value']) . ')';
											break;
									}
									
			//						switch($where['operator']) {
			//							case '=':
			//								$sql .= ' IN (';
			//								break;
			//								
			//							case '<>':
			//								$sql .= ' NOT IN (';
			//								break;
			//						}
			//						
			//						foreach($where['value'] as $subkey => $value) {
			//							$sq .= ':where' . $key . '_' . $subkey . ', ';
			//						}
			//						
			//						$sql = substr($sql, 0, strlen($sql)-2) . ')';
								}
								else {
									// Single Value
									$noVal = false;
									
									switch($where['operator']) {
										default:
										case '=':
											$where['operator'] = '=';
											break;
											
										case '<=>':
											$where['operator'] = '<=>';
											break;
											
										case 'LIKE':
											$where['opwerator'] = 'LIKE';
											break;
											
										case '>':
											$where['operator'] = '>';
											break;
											
										case '>=':
										case '=>':
											$where['operator'] = '>=';
											break;
											
										case '<':
											$where['operator'] = '<';
											break;
											
										case '<=':
										case '>=':
											$where['operator'] = '<=';
											break;
											
										case '!=':
										case '<>':
											$where['operator'] = '<>';
											break;
											
										case 'IS NOT':
											$where['operator'] = 'IS NOT';
											break;
											
										case 'IS NULL':
											$where['operator'] = 'IS NULL';
											$noVal = true;
											break;
											
										case 'IS NOT NULL':
											$where['operator'] = 'IS NOT NULL';
											$noVal = true;
											break;
									}
									
									if($noVal) {
										$whereClause .= ' ' . $where['operator'];
									}
									else {
										$whereClause .= ' ' . $where['operator'] . ' ' . self::valueToString($where['value']);
									}
									//$sql .= ' ' . $where['operator'] . ' :where' . $key;
								}
							}
						}
						
						$sql .= $whereClause;
						
						if(is_array($this->_query['sort'])) {
							foreach($this->_query['sort'] as $key => $sort) {
								if($key == 0) {
									$sql .= ' ORDER BY ';
								}
								
								$sql .= $sort . ', ';
							}
							
							$sql = substr($sql, 0, strlen($sql) - 2);
						}
						
						if($this->_query['limit']) {
							$sql .= ' ' . $this->_query['limit'];
						}
					}
					break;
					
				case self::TARGET_PROCEDURE:
					if($this->_procedure) {
						$sql .= 'CALL ' . $this->_procedure . '(';
						
						foreach($this->_query['params'] as $param => $value) {
							$sql .= $value . ', ';
						}
						
						$sql = substr($sql, 0, strlen($sql)-2) . ')';
					}
					break;
			}
			
			if(!$sql) {
				$resultSet = new Bedrock_Model_ResultSet();
			}
			else {
				// Query Database
				Bedrock_Common_Logger::info('Executing Query: "' . $sql . '"');
				$results = $this->_connection->query($sql);
				
				if($results) {
					$results = $results->fetchAll(PDO::FETCH_ASSOC);
				}
				else {
					$results = array();
				}
				
				// Build Records
				foreach($results as $result) {
					Bedrock_Common_Logger::info('Adding new record to ResultSet.');
					$records[] = new Bedrock_Model_Record($this->_table, $result);
				}
				
				// Build ResultSet
				$resultSet = new Bedrock_Model_ResultSet($records);
				
				// Determine Count
				if($this->_query['limit']) {
					$countSql = 'SELECT COUNT(*) AS count FROM ' . $this->_table . ' ' . $whereClause;
					
					Bedrock_Common_Logger::info('Executing Count Query: "' . $countSql . '"');
					$countResult = $this->_connection->query($countSql)->fetch(PDO::FETCH_ASSOC);
					
					$resultSet->setCountAll($countResult['count']);
				}
				else {
					$resultSet->setCountAll(count($records));
				}
				
				Bedrock_Common_Logger::info('Total Record Count: ' . $resultSet->countAll());
			}
			
			Bedrock_Common_Logger::info(array('ResultSet: "' . $sql . '" (' . $resultSet->count() . ')' , $resultSet), Bedrock_Common_Logger::TYPE_TABLE);
			
			Bedrock_Common_Logger::logExit();
			return $resultSet;
		}
		catch(PDOException $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('A problem with the database connection was encountered.');
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('The Query object could not execute the stored query.');
		}
	}
	
	/**
	 * Executes the assembled query and returns the first result returned.
	 *
	 * @return Bedrock_Model_Record the first record returned from the query
	 */
	public function executeFirst() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = NULL;
			
			$this->limit(0, 1);
			$resultSet = $this->execute();
			
			if(count($resultSet) > 0) {
				$result = $resultSet[0];
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Query_Exception('The Query object could not query for the first record using the stored query.');
		}
	}
	
	/**
	 * Converts the specified value into a string usable in SQL queries.
	 *
	 * @param mixed $value the value to convert
	 * @return string the resulting string
	 */
	public static function valueToString($value) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			
			switch(gettype($value)) {
				case 'array':
					foreach($value as $element) {
						$result .= self::valueToString($element) . ', ';
					}
					
					$result = substr($result, 0, strlen($result)-2);
					break;
					
				case 'boolean':
					$result = $value ? '1' : '0';
					break;
					
				case 'double':
				case 'integer':
					$result = $value + 0;
					break;
					
				case 'NULL':
					$result = 'null';
					break;
					
				default:
				case 'string':
					$result = '\'' . self::sanitize($value) . '\'';
					break;
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
}
?>