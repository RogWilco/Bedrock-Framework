<?php
namespace Bedrock\Common\DataFormat;

/**
 * DataFormat YAML Variant
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 09/26/2008
 * @updated 07/02/2012
 */
class YAML extends \Bedrock\Common\DataFormat {
	/**
	 * Initializes the DataFormat object.
	 *
	 * @param array $data the data to use
	 */
	public function __construct($data = array()) {
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
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\DataFormat\Exception('A problem was encountered while attempting to construct a YAML object from the supplied data.');
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
			foreach($this->_data as $entry) {
				foreach($entry as $key => $value) {
					if(get_class($value) == 'Bedrock\\Common\\DataFormat\\YAML') {
						$result[] = array($key => $value->toArray());
					}
					else {
						$result[] = array($key => $value);
					}
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
	 * Returns the current data as a YAML string.
	 * 
	 * @param string $indent an optional indent string to prepend to each line
	 * @return string the data assembled into a YAML string
	 */
	public function toString($indent = '') {
		try {
			// Setup
			$result = '';
			$t = \Bedrock\Common::TXT_TAB;
			$n = \Bedrock\Common::TXT_NEWLINE;
			
			// Build YAML
			if($indent == '') {
				$result .= '---' . $n;
			}
			
			foreach($this->_data as $entry) {
				foreach($entry as $key => $value) {
					if(get_class($value) == 'Bedrock\\Common\\DataFormat\\YAML') {
						$result .=
							$indent . $key . ':' . $n . 
							$value->toString($indent . $t);
					}
					else {
						$result .= $indent . $key . ': ' . $value . $n;
					}
				}
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\DataFormat\Exception('A problem was encountered while attempting to generate a YAML string.');
		}
	}
}