<?php
/**
 * Form field interface for implementing custom form Field objects.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 12/31/2008
 * @updated 12/31/2008
 */
interface Bedrock_Common_Form_Field_Interface {
	/**
	 * Initializes a new form field with the specified options.
	 *
	 * @param array $options additional objects for the field object
	 * @param string $parentId the parent form or group's ID
	 */
	public function __construct($options, $parentId = '');
	
	/**
	 * Applies any default properties for the current object.
	 */
	public function defaults();
	
	/**
	 * Loads field settings using the specified data. Data can either be
	 * supplied as an array, Config object, or a SimpleXMLElement object.
	 *
	 * @param mixed $arg the data to load as an array, Config object, or SimpleXMLElement object
	 */
	public function load($arg);
	
	/**
	 * Gets the specified property's value.
	 *
	 * @param string $name the name of the desired property
	 * @return mixed the corresponding value
	 */
	public function __get($name);
	
	/**
	 * Sets the specified property to the specified value.
	 *
	 * @param string $name the name of the property to set
	 * @param string $value the value to apply to the property
	 */
	public function __set($name, $value);
	
	/**
	 * Renders the specified property.
	 *
	 * @param string $property the name of the property to render
	 */
	public function render($property = 'input');
}
?>