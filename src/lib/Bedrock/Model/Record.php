<?php
namespace Bedrock\Model;

/**
 * Represents a database record holding data stored in the database. Also
 * handles basic saving/deleting operations.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 08/29/2008
 * @updated 07/02/2012
 */
class Record extends \Bedrock\Model implements \ArrayAccess, \Countable, \SeekableIterator {
	const STATE_UNCHANGED = 0;
	const STATE_CHANGED = 1;
	const STATE_NEW = 2;
	
	protected $_table;
	protected $_columns = array();
	protected $_data = array();
	protected $_state;
	protected $_key_primary;
	
	/**
	 * Initializes the record object.
	 * 
	 * @param \PDO $connection the database connection to use
	 * @param \Bedrock\Model\Table the record's corresponding table
	 * @param array $values the record's values
	 */
	public function __construct($table, $values = array(), $database = NULL) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			parent::__construct($database);
			
			$this->_table = new \Bedrock\Model\Table(array('name' => $table));
			$this->_table->load();
			$this->_columns = $this->_table->getColumns();
			$this->_state = self::STATE_UNCHANGED;
			
			foreach($this->_columns as $column) {
				switch($column->type) {
					case \Bedrock\Model\Column::FIELD_TYPE_BOOL:
						if(is_bool($values[$column->name])) {
							$this->_data[$column->name] = $values[$column->name];
						}
						else {
							$this->_data[$column->name] = ($values[$column->name] == 1 ? true : false);
						}
						break;
						
					default:
						$this->_data[$column->name] = $values[$column->name];
						break;
				}
				
				if($column->primary_key) {
					$this->_key_primary = $column;
					
					if(!$values[$column->name]) {
						$this->_state = self::STATE_NEW;
					}
				}
				
				// Foreign Key Reference?
				
			}
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Record\Exception('A record object could not be initialized.');
		}
	}
	
	/**
	 * Sets the specified field to the specified value. Also flags the record as
	 * being changed.
	 *
	 * @param string $field the field to which the value will be applied
	 * @param mixed $value the value to apply to the field
	 */
	public function __set($field, $value) {
		if(array_key_exists($field, $this->_data)) {
			$this->_data[$field] = $value;
			
			if($this->_state == self::STATE_UNCHANGED) {
				$this->_state = self::STATE_CHANGED;
			}
		}
		else {
			throw new \Bedrock\Model\Record\Exception('The specified field "' . $field . '" was not found.');
		}
	}
	
	/**
	 * Retrieves the corresponding field value for the record, or throws an
	 * exception for nonexistent field names.
	 *
	 * @param string $field the field whose value is desired
	 * @return mixed the corresponding value for the field
	 */
	public function __get($field) {
		if(array_key_exists($field, $this->_data)) {
			return $this->_data[$field];
		}
		else {
			throw new \Bedrock\Model\Record\Exception('The specified field "' . $field . '" was not found.');
		}
	}
	
	/**
	 * Returns the record's primary key column name.
	 *
	 * @return mixed the name of the primary key
	 */
	public function getPrimaryKey() {
		return $this->_key_primary->name;
	}
	
	/**
	 * Returns the requested record property.
	 *
	 * @param string $name the name of the property to retrieve
	 * @return mixed the corresponding property, or NULL if not found
	 */
	public function getProperty($name) {
		switch($name) {
			case 'table':
				return $this->_table->getProperty('name');
				break;
			case 'columns':
				return $this->_columns;
				break;
			default:
				return NULL;
				break;
		}
	}
	
	/**
	 * Returns the record's corresponding Table object.
	 *
	 * @return \Bedrock\Model\Table the table object for the record
	 */
	public function getTable() {
		return $this->_table;
	}
	
	/**
	 * Returns the current Record as an array.
	 *
	 * @return array an array of values
	 */
	public function toArray() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = array();
			
			foreach($this->_columns as $column) {
				$result[$column->name] = $this->_data[$column->name];
			}
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Record\Exception('There was a problem converting the Record to an array.');
		}
	}
	
	/**
	 * Saves the current record to the database.
	 */
	public function save() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			$sql = '';

			if($this->_state == self::STATE_CHANGED) {
				$sql = 'UPDATE ' . self::sanitize($this->_table->getProperty('name')) . ' SET ';
				
				foreach($this->_columns as $column) {
					$sql .= $column->name . ' = ';
					
					switch($column->type) {
						case \Bedrock\Model\Column::FIELD_TYPE_INT:
						case \Bedrock\Model\Column::FIELD_TYPE_FLOAT:
						case \Bedrock\Model\Column::FIELD_TYPE_DOUBLE:
						case \Bedrock\Model\Column::FIELD_TYPE_DECIMAL:
							if($this->_data[$column->name]) {
								$sql .= $this->_data[$column->name] . ', ';
							}
							else {
								$sql .= '\'\', ';
							}
							break;
							
						case \Bedrock\Model\Column::FIELD_TYPE_BOOL:
							$sql .= ($this->_data[$column->name] ? '1' : '0') . ', ';
							break;
							
						case \Bedrock\Model\Column::FIELD_TYPE_DATETIME:
							if(!$this->_data[$column->name]) {
								$sql .= 'NOW(), ';
							}
							else {
								$sql .= '\'' . date('Y-m-d H:i:s', strtotime($this->_data[$column->name])) . '\', ';
							}
							break;
							
						case \Bedrock\Model\Column::FIELD_TYPE_DATE:
							if(!$this->_data[$column->name]) {
								$sql .= 'NOW(), ';
							}
							else {
								$sql .= '\'' . date('Y-m-d', strtotime($this->_data[$column->name])) . '\', ';
							}
							break;
							
						case \Bedrock\Model\Column::FIELD_TYPE_TIME:
							if(!$this->_data[$column->name]) {
								$sql .= 'NOW(), ';
							}
							else {
								$sql .= '\'' . date('H:i:s', strtotime($this->_data[$column->name])) . '\', ';
							}
							break;
							
						default:
							$sql .= '\'' . self::sanitize($this->_data[$column->name]) . '\', ';
							break;
					}
				}
				
				$sql = substr($sql, 0, strlen($sql) - 2) . ' WHERE ' . self::sanitize($this->_key_primary->name) . ' = ' . self::sanitize($this->_data[$this->_key_primary->name]);
			}
			elseif($this->_state == self::STATE_NEW) {
				$sql = 'INSERT INTO ' . self::sanitize($this->_table->getProperty('name')) . ' (';
				
				foreach($this->_columns as $column) {
					if(!$column->primary_key) {
						$sql .= self::sanitize($column->name) . ', ';
					}
				}
				
				$sql = substr($sql, 0, strlen($sql) - 2) . ') VALUES (';
				
				foreach($this->_columns as $column) {
					if(!$column->primary_key) {
						switch($column->type) {
							case \Bedrock\Model\Column::FIELD_TYPE_INT:
							case \Bedrock\Model\Column::FIELD_TYPE_FLOAT:
							case \Bedrock\Model\Column::FIELD_TYPE_DOUBLE:
							case \Bedrock\Model\Column::FIELD_TYPE_DECIMAL:
								if($this->_data[$column->name]) {
									$sql .= $this->_data[$column->name] . ', ';
								}
								else {
									$sql .= '\'\', ';
								}
								break;
								
							case \Bedrock\Model\Column::FIELD_TYPE_BOOL:
								$sql .= ($this->_data[$column->name] ? '1' : '0') . ', ';
								break;
								
							case \Bedrock\Model\Column::FIELD_TYPE_DATETIME:
								if(!$this->_data[$column->name]) {
									$sql .= 'NOW(), ';
								}
								else {
									$sql .= '\'' . date('Y-m-d H:i:s', strtotime($this->_data[$column->name])) . '\', ';
								}
								break;
								
							case \Bedrock\Model\Column::FIELD_TYPE_DATE:
								if(!$this->_data[$column->name]) {
									$sql .= 'NOW(), ';
								}
								else {
									$sql .= '\'' . date('Y-m-d', strtotime($this->_data[$column->name])) . '\', ';
								}
								break;
								
							case \Bedrock\Model\Column::FIELD_TYPE_TIME:
								if(!$this->_data[$column->name]) {
									$sql .= 'NOW(), ';
								}
								else {
									$sql .= '\'' . date('H:i:s', strtotime($this->_data[$column->name])) . '\', ';
								}
								break;
								
							default:
								$sql .= '\'' . self::sanitize($this->_data[$column->name]) . '\', ';
								break;
						}
					}
				}
				
				$sql = substr($sql, 0, strlen($sql) -2) . ')';
			}
			
			\Bedrock\Common\Logger::info('Saving record with query: ' . $sql);
			$this->_connection->exec($sql);
			$this->_state = self::STATE_UNCHANGED;
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\PDOException $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Record\Exception('There was a problem saving the record to the database.');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Record\Exception('An error was encountered and the record could not be saved.');
		}
	}
	
	/**
	 * Deletes the currend record from the database.
	 */
	public function delete() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			if($this->_state == self::STATE_UNCHANGED) {
				$sql = 'DELETE FROM ' . self::sanitize($this->_table->getProperty('name')) . ' WHERE ' . self::sanitize($this->_key_primary->name) . ' = ' . self::sanitize($this->_data[$this->_key_primary->name]);
				\Bedrock\Common\Logger::info('Deleting record with query: ' . $sql); echo $sql;
				$this->_connection->exec($sql);
			}
			elseif($this->_state == self::STATE_CHANGED) {
				throw new \Bedrock\Model\Record\Exception('Unsaved changes found, cannot delete record.');
			}
			else {
				throw new \Bedrock\Model\Record\Exception('Record not found in database.');
			}
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\PDOException $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Record\Exception('A database error was encountered, the record could not be deleted.');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Record\Exception('The record could not be deleted.');
		}
	}
	
	/**
	 * Associates the specified record with the current Record object.
	 *
	 * @param \Bedrock\Model\Record $record the record to associate
	 */
	public function associate($record) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			\Bedrock\Model\Query::associate($this, $record);
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Record\Exception('The record could not be associated.');
		}
	}
	
	/**
	 * Removes any associations between the specified record with the current
	 * Record object.
	 *
	 * @param \Bedrock\Model\Record $record the record to remove associations with
	 */
	public function dissociate($record) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			\Bedrock\Model\Query::dissociate($this, $record);
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Record\Exception('The record could not be dissociated.');
		}
	}
	
	/**
	 * Retrieves all Records in the specified table associated with the current
	 * Record.
	 *
	 * @param string $tableName the name of the table to use
	 * @return \Bedrock\Model\ResultSet any associated records in the table
	 */
	public function associated($tableName, $limit = array()) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			$result = \Bedrock\Model\Query::associated($this, $tableName, $limit);
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Model\Record\Exception('A problem was encountered while checking for associated records.');
		}
	}
	
	/**
	 * Returns whether or not the specified column exists within the Record.
	 *
	 * @param int $offset the column to check
	 * @return boolean whether or not the column exists
	 */
	public function offsetExists($offset) {
		$result = false;
		
		if($this->_data[$offset]) {
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * Returns the requested column value from the Record.
	 *
	 * @param string $offset the specified column value to retrieve
	 * @return string the corresponding value
	 */
	public function offsetGet($offset) {
		if(!$this->offsetExists($offset)) {
			throw new \Bedrock\Model\Record\Exception('The requested column "' . $offset . '" is not a part of this record.');
		}
		
		return $this->_data[$offset];
	}
	
	/**
	 * Sets the specified column to the specified value.
	 *
	 * @param string $offset the offset to use
	 * @param string $value the value to apply
	 */
	public function offsetSet($offset, $value) {
		if(!array_key_exists($offset, $this->_data)) {
			throw new \Bedrock\Model\Record\Exception('The specified column "' . $offset . '" is not a part of this record, the value was not assigned.');
		}
		
		$this->_data[$offset] = $value;
	}
	
	/**
	 * Removes the specified value from the Record.
	 *
	 * @param integer $offset the offset to use
	 */
	public function offsetUnset($offset) {
		if(!array_key_exists($offset, $this->_data)) {
			throw new \Bedrock\Model\Record\Exception('The specified column "' . $offset . '" is not a part of this record, the value was not unset.');
		}
		
		unset($this->_data[$offset]);
	}
	
	/**
	 * Returns the number of columns currently stored within the Record.
	 * 
	 * @return integer the number of columns
	 */
	public function count() {
		return count($this->_data);
	}
	
	/**
	 * Returns the currently selected column.
	 *
	 * @return string the column currently selected
	 */
	public function current() {
		return current($this->_data);
	}
	
	/**
	 * Returns the key of the currently selected column.
	 *
	 * @return integer the current key value
	 */
	public function key() {
		return key($this->_data);
	}
	
	/**
	 * Advances the internal pointer to the next column in the Record.
	 */
	public function next() {
		next($this->_data);
	}
	
	/**
	 * Reverses the internal pointer to the first column in the Record.
	 */
	public function rewind() {
		reset($this->_data);
	}
	
	/**
	 * Returns the column value at the specified index.
	 *
	 * @param string $index the index to seek
	 */
	public function seek($index) {
		// Setup
		$this->rewind();
		$position = 0;
		
		while($position < $index && $this->valid()) {
			$this->next();
			$position++;
		}
		
		if(!$this->valid()) {
			throw new \Bedrock\Model\Record\Exception('Invalid index specified.');
		}
	}
	
	/**
	 * Checks if the current element is valid after a call to the rewind() or
	 * next() functions.
	 *
	 * @return boolean whether or not the pointer currently points to a valid column
	 */
	public function valid() {
		return (current($this->_data) !== false);
	}
}