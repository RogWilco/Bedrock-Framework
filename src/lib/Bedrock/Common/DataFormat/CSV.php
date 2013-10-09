<?php
namespace Bedrock\Common\DataFormat;

/**
 * DataFormat CSV Variant
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 09/26/2008
 * @updated 07/02/2012
 */
class CSV extends \Bedrock\Common\DataFormat {
	/**
	 * Initializes the DataFormat object.
	 *
	 * @param array $data the data to use
	 */
	public function __construct($data = array()) {
		try {
			// Build Data
			foreach($data as $key => $value) {
				if(is_array($value)) {
					$this->_data[$key] = new self($value);
				}
				else {
					$this->_data[$key] = $value;
				}
			}
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\DataFormat\Exception('A problem was encountered while attempting to construct a CSV object from the supplied data.');
		}
	}
	
	/**
	 * Returns the currently stored data as an array.
	 *
	 * @return array the currently stored data
	 */
	public function toArray() {
		try {
			// Setup
			$result = array();
			
			// Build Array
			foreach($this->_data as $key => $value) {
				if(get_class($value) == 'Bedrock\\Common\\DataFormat\\CSV') {
					$result[$key] = $value->toArray();
				}
				else {
					$result[$key] = $value;
				}
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\DataFormat\Exception('A problem was encountered while attempting to generate an array.');
		}
	}
	
	/**
	 * Returns the current data as an CSV string.
	 * 
	 * @return string the data assembled into an CSV string
	 */
	public function toString() {
		try {
			// Setup
			$result = '';
			
			foreach($this->_data as $key => $value) {
				if(get_class($value) == 'Bedrock\\Common\\DataFormat\\CSV') {
					$result .= $value->toString();
				}
				else {
					$result .= $value;
				}
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\DataFormat\Exception('A problem was encountered while attempting to generate a CSV string.');
		}
	}
}