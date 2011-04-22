<?php
/**
 * Form Cache
 * 
 * Used to cache HTML form field values.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 03/29/2008
 * @updated 03/29/2008
 */
class Bedrock_Common_Form_Cache extends Bedrock {
	protected $_cache;
	
	/**
	 * Default Constructor
	 * 
	 * @return void
	 */
	public function __construct() {
		$this->clearCache();
	}
	
	/**
	 * Adds an item to the cache.
	 * 
	 * @param string $field the field to add to the cache
	 * @return void
	 */
	public function addItem($field) {
		$this->_cache[$field] = $this->_getValue($field);
	}
	
	/**
	 * Adds the specified file field to the cache.
	 * 
	 * @param string $field the field to add to the cache
	 * @param string $tempDir a temporary directory to store the file
	 * @return void
	 */
	public function addFile($field, $tempDir) {
		if(is_uploaded_file($_FILES[$field]['tmp_name'])) {
			$this->_cache[$field] = $_FILES[$field];
			
			$tempName = substr(strrchr($_FILES[$field]['tmp_name'], DIRECTORY_SEPARATOR), 1);
			$fileName = "avatar_".$tempName;
			$dest = $tempDir.DIRECTORY_SEPARATOR.$fileName;
			
			if(move_uploaded_file($_FILES[$field]['tmp_name'], $dest)) {
				$this->_cache[$field]['tmp_name'] = $dest;
			}
		}
	}
	
	/**
	 * Retrieves the specified item from the cache, or an empty value if the
	 * item is not cached.
	 * 
	 * @param string $field the field to retrieve from the cache
	 * @return mixed the specified field's cached value
	 */
	public function getItem($field) {
		if(array_key_exists($field, $this->_cache)) {
			return $this->_cache[$field];
		}
		else {
			return "";
		}
	}
	
	/**
	 * Manually inserts an item into the cache.
	 * 
	 * @param string $name the name of the field to use
	 * @param mixed $value the value to cache
	 * @return void
	 */
	public function insertItem($name, $value) {
		$this->_cache[$name] = $value;
	}
	
	/**
	 * Removes an item from the cache.
	 * 
	 * @param string $name the name of the field to remove
	 * @return void
	 */
	public function deleteItem($field) {
		if(array_key_exists($field, $this->_cache)) {
			unset($this->_cache[$field]);
		}
	}
	
	/**
	 * Retrieves the specified field's value (or values when there is more than
	 * one value stored as an array).
	 * 
	 * @param string $field the field whose value(s) are to be returned
	 * @return array the specified field's values
	 */
	protected function _getValue($field) {
		if(is_array($_POST[$field])) {
			foreach($_POST[$field] as $entry) {
				$result[] = trim($entry);
			}
		}
		else {
			$result = trim($_POST[$field]);
		}
		
		return $result;
	}
	
	/**
	 * Formates the specified cached field.
	 * 
	 * @param string $field the field to format
	 * @param string $option the type of formatting to appply (default is "ucwords")
	 * @return void
	 */
	public function formatItem($field, $option = "ucwords") {
		if(isset($this->_cache[$field])) {
			switch($option) {
				case("ucwords"):
					$this->_cache[$field] = ucwords($this->_cache[$field]);
					break;
				case("lowercase"):
					$this->_cache[$field] = strtolower($this->_cache[$field]);
					break;
				case("uppercase"):
					$this->_cache[$field] = strtoupper($this->_cache[$field]);
					break;
			}
		}
	}
	
	/**
	 * Checks if the specified field is cached. Returns true if it is found, and
	 * false if it is not.
	 * 
	 * @param string $field the name of the field to check
	 * @return boolean the result, true if it exists, false if it isn't found
	 * @return void
	 */
	public function isCached($field) {
		if(isset($this->_cache[$field])) {
			return true;
		}
		else {
			return false;
		}
	}
	
	/**
	 * Returns the form cache.
	 * 
	 * @return array an array containing all form entries
	 */
	public function getCache() {
		return $this->_cache;
	}
	
	/**
	 * Clears the current form cache.
	 * 
	 * @return void
	 */
	public function clearCache() {
		$this->_cache = array();
	}
}
?>