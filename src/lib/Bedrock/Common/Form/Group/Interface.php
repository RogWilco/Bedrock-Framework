<?php
/**
 * Form field group interface for implementing custom form field groups.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 12/31/2008
 * @updated 12/31/2008
 */
interface Bedrock_Common_Form_Group_Interface extends ArrayAccess, Countable, SeekableIterator {
	/**
	 * Initializes the field group.
	 *
	 * @param mixed $options initialization options for the field group
	 */
	public function __construct($options = null);
	
	/**
	 * Applies any default properties for the current object.
	 */
	public function defaults();
	
	/**
	 * Loads group settings using the specified data. Data can either be supplied
	 * as an array, Config object, or a SimpleXMLElement object.
	 *
	 * @param mixed $arg the data to load as an array, Config object, or SimpleXMLElement object
	 */
	public function load($arg);
	
	/**
	 * Gets the specified field's value.
	 *
	 * @param string $name the field whose value is to be returned
	 * @return mixed the corresponding value
	 */
	public function __get($name);
	
	/**
	 * Sets the specified field value.
	 *
	 * @param string $name the name of the field for which the value will be set
	 * @param mixed $value the value to assign to the specified field
	 */
	public function __set($name, $value);
	
	/**
	 * Retrieves the requested Field by name or index, or returns all stored
	 * Fields if no index/name is given.
	 *
	 * @param mxied $fieldIndex either the index of the Field, or the Field's name
	 * @return mixed either the corresponding Field, all Fields, or null if none are found
	 */
	public function fields($fieldIndex = null);
	
	/**
	 * Retrieves the requested switch block by index, or returns all stored
	 * switch blocks if no index is given.
	 *
	 * @param integer $switchIndex the index of the switch block entry
	 * @return array either the corresponding switch block, all switch blocks, or null if none are found
	 */
	public function switches($switchIndex = null);
}
?>