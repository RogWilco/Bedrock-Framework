<?php
namespace Bedrock\Common\Utils\Data;

/**
 * A utilities class for working with the JSON format.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 10/22/2008
 * @updated 07/02/2012
 */
class JSON extends \Bedrock {
	const FORMAT_NONE = 0;
	const FORMAT_INDENT = 1;
	const FORMAT_ROWS = 2;
	const FORMAT_ROW = 3;
	
	
	/**
	 * Converts the supplied array into a valid JSON string.
	 *
	 * @param array $array the array to convert
	 * @param integer $format whether or not to apply indentation/newlines
	 * @param string $indentPrefix a string to prefix to every new line for nested calls
	 * @return string the assembled JSON string
	 */
	public static function encode($array, $format = self::FORMAT_INDENT, $indentPrefix = '') {
		try {
			// Setup
			$t = \Bedrock\Common::TXT_TAB;
			$n = \Bedrock\Common::TXT_NEWLINE;
			$json = '';
			
			if($format === self::FORMAT_NONE) {
				$t = '';
				$n = '';
			}
			
			// Build JSON String
			if(count($array)) {
				if(self::isAssoc($array)) {
					$json .=  '{' . $n;
					
					foreach($array as $key => $value) {
						if(gettype($value) == 'array') {
							if($format === self::FORMAT_ROWS && $key == 'rows') {
								$json .= $indentPrefix . $t . $key  . ': ' . self::encode($value, self::FORMAT_ROW) . ', ' . $n;
							}
							else {
								$json .= $indentPrefix . $t . $key . ': ' . self::encode($value, $format, $indentPrefix . $t) . ', ' . $n;
							}
						}
						else {
							$json .= $indentPrefix . $t . $key . ': ' . self::formatValue($value) . ', ' . $n;
						}
					}
					
					if(!$n) {
						$json = substr($json, 0, strlen($json)-2);
					}
					else {
						$json = substr($json, 0, strlen($json)-3) . $n;
					}
					
					$json .= $indentPrefix . '}';
					
				}
				else {
					$json .= '[' . $n;
					
					foreach($array as $key => $value) {
						if(gettype($value) == 'array') {
							if($format === self::FORMAT_ROW) {
								$json .= $indentPrefix . $t . self::encode($value, self::FORMAT_NONE) . ', ' . $n;
							}
							else {
								$json .= $indentPrefix . $t . self::encode($value, $format, $indentPrefix . $t) . ', ' . $n;
							}
						}
						else {
							$json .= $indentPrefix . $t . self::formatValue($value) . ', ' . $n;
						}
					}
					
					if(!$n) {
						$json = substr($json, 0, strlen($json)-2);
					}
					else {
						$json = substr($json, 0, strlen($json)-3) . $n;
					}
					
					
					$json .= $indentPrefix . ']';
				}
			}
			
			return $json;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Exception('A problem was encountered while attempting to encode the data to JSON.');
		}
	}
	
	/**
	 * Determines whether or not the specified array is associative or
	 * sequential.
	 *
	 * @param array $array the array to check
	 * @return boolean whether or not the array is associative
	 */
	public static function isAssoc($array) {
		return array_keys($array) != range(0, count($array) - 1);
	}
	
	/**
	 * Formats the supplied value for use in a JSON string.
	 *
	 * @param mixed $value the value to format
	 * @param integer $format whether or not to apply formatting if value is an array
	 * @param string $indentPrefix a string to prefix to every new line for nested calls
	 * @return string the formatted value
	 */
	public static function formatValue($value) {
		// Setup
		$result = '';
		
		switch(gettype($value)) {
			case 'boolean':
				$result = $value ? 'true' : 'false';
				break;
				
			case 'double':
			case 'integer':
				$result = $value + 0;
				break;
				
			case 'string':
				$result = '\'' . $value . '\'';
				break;
				
			default:
			case 'NULL':
				$result = 'null';
				break;
		}
		
		return $result;
	}
}