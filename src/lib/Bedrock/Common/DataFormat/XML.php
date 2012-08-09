<?php
namespace Bedrock\Common\DataFormat;

/**
 * DataFormat XML Variant
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 09/26/2008
 * @updated 07/02/2012
 */
class XML extends \Bedrock\Common\DataFormat {
	/**
	 * Initializes the DataFormat object.
	 *
	 * @param array $data the data to use
	 */
	public function __construct($data = array()) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Build Data
			foreach($data as $entry) {
				foreach($entry as $key => $value) {
					if(is_array($value)) {
						$this->_data[][$key] = new self($value);
					}
					else {
						$this->_data[][$key] = $value;
					}
				}
			}
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\DataFormat\Exception('A problem was encountered while attempting to construct an XML object from the supplied data.');
		}
	}
	
	/**
	 * Returns the currently stored data as an array.
	 *
	 * @return array the currently stored data
	 */
	public function toArray() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = array();
			
			// Build Array
			foreach($this->_data as $entry) {
				foreach($entry as $key => $value) {
					if(get_class($value) == 'Bedrock\\Common\\DataFormat\\XML') {
						$result[] = array($key => $value->toArray());
					}
					else {
						$result[] = array($key => $value);
					}
				}
			}
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\DataFormat\Exception('A problem was encountered while attempting to generate an array.');
		}
	}
	
	/**
	 * Returns the current data as an XML string.
	 * 
	 * @param string $indent an optional indent string to prepend to each line
	 * @return string the data assembled into an XML string
	 */
	public function toString($indent = '') {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$t = \Bedrock\Common::TXT_TAB;
			$n = \Bedrock\Common::TXT_NEWLINE;
			
			// Build XML
			foreach($this->_data as $entry) {
				foreach($entry as $key => $value) {
					if(get_class($value) == 'Bedrock\\Common\\DataFormat\\XML') {
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
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\DataFormat\Exception('A problem was encountered while attempting to generate an XML string.');
		}
	}
}