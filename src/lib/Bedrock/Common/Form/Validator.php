<?php
namespace Bedrock\Common\Form;

/**
 * Applies validation rules to a form and stores any failures.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 01/02/2009
 * @updated 07/02/2012
 */
class Validator extends \Bedrock {
	protected $_errors = array();
	
	/**
	 * Initializes the form validator object.
	 */
	public function __construct() {
		try {
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Validates the specified form.
	 *
	 * @param string $formDefinition the target form's definition file
	 */
	public function validate($formDefinition) {
		try {
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Retrieves the specified value or property.
	 *
	 * @param string $name the name of a value or property to retrieve
	 */
	public function __get($name) {
		try {
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Sets the specified value or property (if writeable).
	 *
	 * @param string $name the name of the value or property to set
	 * @param mixed $value the value to apply
	 */
	public function __set($name, $value) {
		try {
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Whether or not the current form has any errors.
	 * 
	 * @param string $field the field to check, leave blank to check entire form
	 * @return boolean the result of the error check
	 */
	public function hasErrors($field = null) {
		try {
			// Setup
			$result = false;
			
			// Check for Errors
			if($field === null) {
				$result = (bool) count($this->_errors);
			}
			else {
				
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Clears all currently stored errors.
	 */
	public function clearErrors() {
		try {
			$this->_errors = array();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
}