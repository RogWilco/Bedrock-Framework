<?php
/**
 * Applies validation rules to a form and stores any failures.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 01/02/2009
 * @updated 01/02/2009
 */
class Bedrock_Common_Form_Validator extends Bedrock {
	protected $_errors = array();
	
	/**
	 * Initializes the form validator object.
	 */
	public function __construct() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Validates the specified form.
	 *
	 * @param string $formDefinition the target form's definition file
	 */
	public function validate($formDefinition) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Retrieves the specified value or property.
	 *
	 * @param string $name the name of a value or property to retrieve
	 */
	public function __get($name) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Sets the specified value or property (if writeable).
	 *
	 * @param string $name the name of the value or property to set
	 * @param mixed $value the value to apply
	 */
	public function __set($name, $value) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Whether or not the current form has any errors.
	 * 
	 * @param string $field the field to check, leave blank to check entire form
	 * @return boolean the result of the error check
	 */
	public function hasErrors($field = null) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = false;
			
			// Check for Errors
			if($field === null) {
				$result = (bool) count($this->_errors);
			}
			else {
				
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Clears all currently stored errors.
	 */
	public function clearErrors() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			$this->_errors = array();
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
}
?>