<?php
/**
 * DataFormat CSV Variant
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 09/26/2008
 * @updated 09/26/2008
 */
class Bedrock_Common_DataFormat_CSV extends Bedrock_Common_DataFormat {
	/**
	 * Initializes the DataFormat object.
	 *
	 * @param array $data the data to use
	 */
	public function __construct($data = array()) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Build Data
			foreach($data as $key => $value) {
				if(is_array($value)) {
					$this->_data[$key] = new Bedrock_Common_DataFormat_CSV($value);
				}
				else {
					$this->_data[$key] = $value;
				}
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_DataFormat_Exception('A problem was encountered while attempting to construct a CSV object from the supplied data.');
		}
	}
	
	/**
	 * Returns the currently stored data as an array.
	 *
	 * @return array the currently stored data
	 */
	public function toArray() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = array();
			
			// Build Array
			foreach($this->_data as $key => $value) {
				if(get_class($value) == 'Bedrock_Common_DataFormat_CSV') {
					$result[$key] = $value->toArray();
				}
				else {
					$result[$key] = $value;
				}
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_DataFormat_Exception('A problem was encountered while attempting to generate an array.');
		}
	}
	
	/**
	 * Returns the current data as an CSV string.
	 * 
	 * @return string the data assembled into an CSV string
	 */
	public function toString() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			
			foreach($this->_data as $key => $value) {
				if(get_class($value) == 'Bedrock_Common_DataFormat_CSV') {
					$result .= $value->toString();
				}
				else {
					$result .= $value;
				}
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_DataFormat_Exception('A problem was encountered while attempting to generate a CSV string.');
		}
	}
}
?>