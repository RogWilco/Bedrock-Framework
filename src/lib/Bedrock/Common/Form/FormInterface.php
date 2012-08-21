<?php
namespace Bedrock\Common\Form;

/**
 * Form interface for implementing custom form management classes.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 12/31/2008
 * @updated 07/02/2012
 */
interface FormInterface extends \ArrayAccess, \Countable, \SeekableIterator {
	/**
	 * Initializes a new form with the specified options.
	 *
	 * @param mixed $options additional options for the form object, either as an array, a Config object, or a string containing the path to an XML form definition file
	 */
	public function __construct($options = null);
	
	/**
	 * Applies any default properties for the current object.
	 */
	public function defaults();
	
	/**
	 * Loads form settings using the specified data. Data can either be supplied
	 * as an array, Config object, or a path to an XML form definition file.
	 *
	 * @param mixed $arg the data to load as an array, Config object, or a path to an XML form definition file
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
	 * Adds a new class-to-field-type mapping to the Form.
	 *
	 * @param string $type the new type to map
	 * @param string $className the class to map to
	 */
	public static function addMapping($type, $className);
	
	/**
	 * Retrieves the specified type/class mapping, or false if a mapping isn't
	 * found.
	 *
	 * @param string $type the type to search for
	 * @return string the name of the corresponding class
	 */
	public static function getMapping($type);
	
	/**
	 * Retrieves the requested Group by name or index, or returns all stored
	 * Groups if no index/namem is given.
	 *
	 * @param mixed $groupIndex either the index of the Group, or the Group's name
	 * @return mixed either the corresponding Group, all Groups, or null if none are found
	 */
	public function groups($groupIndex = null);
	
	/**
	 * Retrieves the requested Field by name or index, or returns all stored
	 * Fields if no index/name is given.
	 *
	 * @param mixed $fieldIndex either the index of the Field, or the Field's name
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
	
	/**
	 * Validates the current form and returns the result of the validation process.
	 *
	 * @return boolean whether or not the form passed validation
	 */
	public function validate();
}