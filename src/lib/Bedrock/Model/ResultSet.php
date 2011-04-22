<?php
/**
 * Stores a result set returned from a database query.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 09/09/2008
 * @updated 09/09/2008
 */
class Bedrock_Model_ResultSet extends Bedrock_Model implements ArrayAccess, Countable, SeekableIterator {
	protected $_records = array();
	
	/**
	 * Initializes a resultset collection.
	 * 
	 * @param array $records an array of records to add to the ResultSet
	 */
	public function __construct($records = array()) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($records) {
				foreach($records as $record) {
					$this->add($record);
				}
			}
			
			parent::__construct();
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_ResultSet_Exception('A result set cound not be initialized.');
		}
	}
	
	/**
	 * Returns the current ResultSet as an array.
	 *
	 * @return array an array of rows/values
	 */
	public function toArray() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = array();
			
			if($this->count()) {
				foreach($this->_records as $record) {
					$result[] = $record->toArray();
				}
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_ResultSet_Exception('There was a problem converting the ResultSet to an array.');
		}
	}
	
	/**
	 * Returns whether or not the specified record exists within the ResultSet.
	 *
	 * @param int $offset the row to check
	 * @return boolean whether or not the row exists
	 */
	public function offsetExists($offset) {
		$result = false;
		
		if(is_numeric($offset) && $this->_records[$offset]) {
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * Returns the requested row from the ResultSet.
	 *
	 * @param integer $offset the specified row to retrieve
	 * @return Bedrock_Model_Record the corresponding record
	 */
	public function offsetGet($offset) {
		if(!is_numeric($offset)) {
			throw new Bedrock_Model_ResultSet_Exception('Records can only be accessed using a numeric offset.');
		}
		elseif(!$this->offsetExists($offset)) {
			throw new Bedrock_Model_ResultSet_Exception('A record does not exist at the requested offset of ' . $offset);
		}
		
		return $this->_records[$offset];
	}
	
	/**
	 * Sets the specified row to the specified Record object.
	 *
	 * @param integer $offset the offset to use
	 * @param Bedrock_Model_Record $value the record to add
	 */
	public function offsetSet($offset, $value) {
		if(!is_numeric($offset)) {
			throw new Bedrock_Model_ResultSet_Exception('Records can only be set using a numeric offset.');
		}
		elseif(get_class($value) != 'Bedrock_Model_Record') {
			throw new Bedrock_Model_ResultSet_Exception('Attempted to add a non-record to a ResultSet object.');
		}
		
		$this->_records[$offset] = $value;
	}
	
	/**
	 * Removes the specified Record row from the ResultSet.
	 *
	 * @param integer $offset the offset to use
	 */
	public function offsetUnset($offset) {
		if(!is_numeric($offset)) {
			throw new Bedrock_Model_ResultSet_Exception('Records can only be set using a numeric offset.');
		}
		
		unset($this->_records[$offset]);
	}
	
	/**
	 * Returns the currently selected Record object.
	 *
	 * @return Bedrock_Model_Record the record object currently selected
	 */
	public function current() {
		return current($this->_records);
	}
	
	/**
	 * Returns the key of the currently selected Record object.
	 *
	 * @return integer the current key value
	 */
	public function key() {
		return key($this->_records);
	}
	
	/**
	 * Advances the internal pointer to the next record in the ResultSet.
	 */
	public function next() {
		next($this->_records);
	}
	
	/**
	 * Reverses the internal pointer to the previous record in the ResultSet.
	 */
	public function prev() {
		prev($this->_records);
	}
	
	/**
	 * Reverses the internal pointer to the first record in the ResultSet.
	 */
	public function rewind() {
		reset($this->_records);
	}
	
	/**
	 * Returns the record object at the specified index.
	 *
	 * @param Bedrock_Model_Record $index
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
			throw new Bedrock_Model_ResultSet_Exception('Invalid index specified.');
		}
	}
	
	/**
	 * Checks if the current element is valid after a call to the rewind() or
	 * next() functions.
	 *
	 * @return boolean whether or not the pointer currently points to a valid record object
	 */
	public function valid() {
		return (current($this->_records) !== false);
	}
	
	/**
	 * Adds a record to the resultset.
	 *
	 * @param Bedrock_Model_Record $record a record object to add to the resultset
	 */
	public function add($record) {
		if(get_class($record) != 'Bedrock_Model_Record') {
			throw new Bedrock_Model_ResultSet_Exception('Attempted to add a non-record object as a record.');
		}
		
		$this->_records[] = $record;
	}
	
	/**
	 * Clears the resultset of all stored records
	 */
	public function clear() {
		$this->_records = array();
	}
	
	/**
	 * Returns the number of records currently stored in the resultset.
	 * 
	 * @return integer the number of record objects
	 */
	public function count() {
		return count($this->_records);
	}
	
	/**
	 * Sets the count of related records if the resultset object was created
	 * from a Query.
	 *
	 * @param integer $count the number of related records
	 */
	public function setCountAll($count) {
		$this->_fullcount = $count;
	}
	
	/**
	 * Returns the total count of all related records if the resultset was
	 * created from a Query.
	 * 
	 * @return integer the number of related records
	 */
	public function countAll() {
		return $this->_fullcount;
	}
}
?>