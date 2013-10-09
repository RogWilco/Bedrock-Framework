<?php
namespace Bedrock\Common\Form;

/**
 * Base rule class containing a common set of form validation rules.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 01/02/2009
 * @updated 07/02/2012
 */
class Rule extends \Bedrock {
	/**
	 * Determines whether or not the specified value is set.
	 *
	 * @param mixed $value the value to check
	 * @return boolean the result of the check
	 */
	public static function required($value) {
		try {
			// Setup
			$result = false;
			
			// Check Value
			if($value != '') {
				$result = true;
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the value contains only numeric characters.
	 *
	 * @param mixed $value the value to check
	 * @return boolean the result of the check
	 */
	public static function numeric($value) {
		try {
			// Check Value
			$result = is_numeric($value);
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the value contains only alphabetic characters.
	 *
	 * @param mixed $value the value to check
	 * @return boolean the result of the check
	 */
	public static function alphabetic($value) {
		try {
			// Setup
			$result = false;
			$pattern = "/^[a-zA-Z]+$/";
			
			if(!preg_match($pattern, $value)) {
				$result = false;
			}
			else {
				$result = true;
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the value contains only alphanumeric characters.
	 *
	 * @param mixed $value the value to check
	 * @return boolean the result of the check
	 */
	public static function alphanumeric($value) {
		try {
			// Check Value
			$result = ctype_alnum($value);
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the value is greater than or equal to the specified minimum
	 * length.
	 *
	 * @param mixed $value the value to check
	 * @param integer $length the minimum length for the value
	 * @return boolean the result of the check
	 */
	public static function minLength($value, $length) {
		try {
			// Setup
			$result = false;
			
			if(strlen($value) >= $length) {
				$result = true;
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the value is less than or equal to the specified minimum
	 * length.
	 *
	 * @param mixed $value the value to check
	 * @param integer $length the maximum length for the value
	 * @return boolean the result of the check
	 */
	public static function maxLength($value, $length) {
		try {
			// Setup
			$result = false;
			
			// Check Value
			if(strlen($value) <= $length) {
				$result = true;
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the two specified values match.
	 *
	 * @param mixed $valueA the first value to check
	 * @param mixed $valueB the second value to check
	 * @return boolean the result of the check
	 */
	public static function match($valueA, $valueB) {
		try {
			// Check Values
			$result = $valueA === $valueB;
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the two specified values are similar (regardless of case).
	 *
	 * @param mixed $valueA the first value to check
	 * @param mixed $valueB the second value to check
	 * @return boolean the result of the check
	 */
	public static function like($valueA, $valueB) {
		try {
			// Setup
			$result = false;
			
			// Check Values
			if(strcmp(strtolower($valueA), strtolower($valueB)) == 0) {
				$result = true;
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the value matches any of the specified possible values.
	 *
	 * @param mixed $value the value to check
	 * @param array $allowedValues an array of possible values
	 * @return boolean the result of the check
	 */
	public static function whiteList($value, $allowedValues) {
		try {
			// Setup
			$result = false;
			
			// Check Allowed Values
			if(in_array($value, $allowedValues)) {
				$result = true;
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the value matches any of the specified disallowed values.
	 *
	 * @param mixed $value the value to check
	 * @param array $disallowedValues an array of disallowed values
	 * @return boolean the result of the check
	 */
	public static function blackList($value, $disallowedValues) {
		try {
			// Setup
			$result = true;
			
			// Check Disallowed Values
			if(in_array($value, $disallowedValues)) {
				$result = false;
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the value is formatted as a valid email address.
	 *
	 * @param mixed $value the value to check
	 * @return boolean the result of the check
	 */
	public static function email($value) {
		try {
			// Setup
			$result = false;
			$pattern = '/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/';
			
			// Check Value
			if(preg_match($pattern, $value)) {
				$result = true;
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the value is a valid phone number for the specified locale.
	 *
	 * @param mixed $value the value to check
	 * @param string $locale the locale to use, defaults to US
	 * @return boolean the result of the check
	 */
	public static function phone($value, $locale = 'US') {
		try {
			// Setup
			$result = false;
			$pattern = '/[^\d\s]/';
			$numeric = self::numeric($value);
			
			// Clear Non-Numeric Characters
			$value = preg_replace($pattern, '', $value); 
			
			// Check Value
			switch($locale) {
				default:
				case 'US':
					// Without Country Code
					if(strlen($value) == 10 && $numeric) {
						$result = true;
					}
					
					// With Country Code
					elseif(strlen($value) == 11 && $numeric && substr($value, 0, 1) == 1) {
						$result = true;
					}
					break;
					
				// @todo implement support for additional locales
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the value is a valid postal code for the specified locale.
	 *
	 * @param mixed $value the value to check
	 * @param string $locale the locale to use, defaults to US
	 * @return boolean the result of the check
	 */
	public static function postalCode($value, $locale = 'US') {
		try {
			// Setup
			$result = false;
			$length = strlen($value);
			$numeric = self::numeric($value);
			
			// Check Value
			switch($locale) {
				default:
				case 'US':
					if($length == (5 || 9) && $numeric) {
						$result = true;
					}
					break;
					
				// @todo implement support for additional locales
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Checks if the two values match and if they are valid for a password. 
	 *
	 * @param mixed $valueA the first value to check
	 * @param mixed $valueB the second value to check
	 * @return boolean the result of the check
	 */
	public static function password($valueA, $valueB) {
		try {
			// Setup
			$result = false;
			
			// Check Match
			if($valueA === $valueB) {
				// Valid Password?
				
				$result = true;
			}
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
}