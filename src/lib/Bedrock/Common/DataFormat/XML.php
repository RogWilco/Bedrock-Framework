<?php
/**
 * DataFormat XML Variant
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 09/26/2008
 * @updated 09/26/2008
 */
class Bedrock_Common_DataFormat_XML extends Bedrock_Common_DataFormat {
	/**
	 * Initializes the DataFormat object.
	 *
	 * @param array $data the data to use
	 */
	public function __construct($data = array()) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Build Data
			foreach($data as $entry) {
				foreach($entry as $key => $value) {
					if(is_array($value)) {
						$this->_data[][$key] = new Bedrock_Common_DataFormat_XML($value);
					}
					else {
						$this->_data[][$key] = $value;
					}
				}
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_DataFormat_Exception('A problem was encountered while attempting to construct an XML object from the supplied data.');
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
			foreach($this->_data as $entry) {
				foreach($entry as $key => $value) {
					if(get_class($value) == 'Bedrock_Common_DataFormat_XML') {
						$result[] = array($key => $value->toArray());
					}
					else {
						$result[] = array($key => $value);
					}
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
	 * Returns the current data as an XML string.
	 * 
	 * @param string $indent an optional indent string to prepend to each line
	 * @return string the data assembled into an XML string
	 */
	public function toString($indent = '') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$t = Bedrock_Common::TXT_TAB;
			$n = Bedrock_Common::TXT_NEWLINE;
			
			// Build XML
			foreach($this->_data as $entry) {
				foreach($entry as $key => $value) {
					if(get_class($value) == 'Bedrock_Common_DataFormat_XML') {
						$result .=
							$indent . '<' . $key . '>' . $n . 
							$value->toString($indent . $t) .
							$indent . '</' . $key . '>' . $n;
					}
					else {
						$result .= $indent . '<' . $key . '>' . $value . '</' . $key . '>' . $n;
					}
				}
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_DataFormat_Exception('A problem was encountered while attempting to generate an XML string.');
		}
	}
}
?>