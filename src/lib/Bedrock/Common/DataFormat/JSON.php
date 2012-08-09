<?php
namespace Bedrock\Common\DataFormat;

/**
 * DataFormat JSON Variant
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 09/26/2008
 * @updated 07/02/2012
 */
class JSON extends \Bedrock\Common\DataFormat {
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
						if($this->isMultidimArray($value)) {
							$this->_data[][$key] = new self($value);
						}
						elseif(is_array($value)) {
							$this->_data[][$key] = $value;
						}
						else {
							$this->_data[][$key] = new self($value);
						}
					}
					else {
						$this->_data[][$key] = $value;
					}
				}
			}
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\DataFormat\Exception('A problem was encountered while attempting to construct a JSON object from the supplied data.');
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
					if(get_class($value) == 'Bedrock\\Common\\DataFormat\\JSON') {
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
	 * Returns the current data as a JSON string.
	 * 
	 * @param string $indent an optional indent string to prepend to each line
	 * @return string the data assembled into a JSON string
	 */
	public function toString($indent = '', $isArray = false) {
		try {
			// Setup
			$result = '';
			$t = \Bedrock\Common::TXT_TAB;
			$n = \Bedrock\Common::TXT_NEWLINE;
			
			// Build JSON
			if($indent == '') {
				$ind = $t;
				$result .= '{' . $n;
			}
			else {
				$ind = $indent . $indent;
				$result .= $indent . '{' . $n;
			}
			
			foreach($this->_data as $entry) {
				foreach($entry as $key => $value) {
					if(get_class($value) == 'Bedrock\\Common\\DataFormat\\JSON') {
						$result .=
							$ind . $key . ':' . $n . 
							$value->toString($indent . $t) . ',';
					}
					elseif(is_object($value) && get_class($value) == 'Bedrock\\Model\\ResultSet') {
						$result .= $ind . $key . ': [' . $n;
						
						foreach($value as $record) {
							$result .= $ind . $t . '{' . $record->getPrimaryKey() . ': ' . $record->{$record->getPrimaryKey()} . ', cell: [';
							
							foreach($record as $column => $val) {
								$result .= $this->formatValue($val) . ', ';
							}
							
							$result = substr($result, 0, strlen($result)-2) . ']';
							
							$result .= '},' . $n;
						}
						
						$result = substr($result, 0, strlen($result)-2) . $n;
						$result .= $ind . ']' . $n;
					}
					else {
						$result .= $ind . $key . ': ' . $this->formatValue($value) . ',' . $n;
					}
				}
			}
			
			$result = substr($result, 0, strlen($result)-2) . $n;
			
			if($indent == '') {
				$result .= '}' . $n;
			}
			else {
				$result .= $indent . '}' . $n;
			}
			
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\DataFormat\Exception('A problem was encountered while attempting to generate a JSON string.');
		}
	}
	
	/**
	 * Formats a vallue depending on its data type (for use when building a
	 * JSON string.
	 *
	 * @param mixed $value the value to format
	 * @return mixed the formatted value
	 */
	protected function formatValue($value) {
		// Setup
		$result = '';
		$t = \Bedrock\Common::TXT_TAB;
		$n = \Bedrock\Common::TXT_NEWLINE;
		
		// Apply Formatting
		if(is_numeric($value)) {
			$result = $value;
		}
		elseif(is_array($value)) {
			$result = '[';
			
			foreach($value as $item) {
				$result .= $this->formatValue($item) . ', ';
			}
			
			$result = substr($result, 0, strlen($result)-2);
			$result .= ']';
		}
		elseif(is_bool($value)) {
			$result = $value ? 'true' : 'false';
		}
		else {
			$result = '\'' . $value . '\'';
		}
		
		return $result;
	}
	
	/**
	 * Determines whether or not the specified array is multidimensional.
	 *
	 * @param array $array the array to check
	 * @return boolean whether or not the array is multidimensional
	 */
	protected function isMultidimArray($array) {
		// Setup
		$result = false;
		
		foreach($array as $value) {
			if(is_array($value)) {
				$result = true;
				break;
			}
		}
		
		return $result;
	}
}