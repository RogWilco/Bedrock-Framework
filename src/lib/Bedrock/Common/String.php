<?php
namespace Bedrock\Common;

/**
 * A general utilities class for string manipulation, also offerring a more
 * advanced String object.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 05/11/2009
 * @updated 07/02/2012
 */
class String extends \Bedrock {
	protected $_value = '';

	/**
	 * Initializes a new String object.
	 *
	 * @param string $value a value for the String
	 */
	public function __construct($value = '') {
		$this->_value = (string) $value;
	}

	/**
	 * Generates a random string of characters of the specified length.
	 *
	 * @param integer $length the number of characters to return
	 * @param string $allowed a string of allowed characters (defaults to numbers and letters)
	 * @return string the generated string
	 */
	public static function random($length = 0, $allowed = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789') {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$allowedLength = strlen($allowed)-1;

			for($i = 0; $i < $length; $i++) {
				$result .= $allowed[mt_rand(0, $allowedLength)];
			}

			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}

	/**
	 * Simplifies a string, replacing spaces with underscores, removing
	 * punctuation, etc.
	 *
	 * @param string $string the string to simplify
	 * @return string the simplified string
	 */
	public static function simplify($string) {
		\Bedrock\Common\Logger::logEntry();

		try {
			// Setup
			$result = $string;

			// Simplify String
			$result = trim($result);
			$result = str_replace(' ', '_', $result);
			$result = strtolower($result);

			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}

	/**
	 * Maps to the standard PHP "addcslashes" function.
	 *
	 * @param string $charlist a list of characters to be escaped
	 * @return string the escaped string
	 */
	public function addcslashes($charlist = null) {
		return addcslashes($this->_value, $charlist);
	}

	/**
	 * Maps to the standard PHP "addslashes" function.
	 *
	 * @return string the escaped string
	 */
	public function addslashes() {
		return addslashes($this->_value);
	}

	/**
	 * Maps to the standard PHP "explode" function.
	 *
	 * @param string $delimiter the boundary string
	 * @param integer $limit the number of elements to return
	 * @return array the resulting array
	 */
	public function explode($delimiter = '', $limit = null) {
		return explode($delimiter, $this->_value, $limit);
	}

	/**
	 * An alias of String::strlen()
	 *
	 * @return integer the length of the current string
	 */
	public function length() {
		return $this->strlen();
	}

	/**
	 * Maps to the standard PHP "strlen" function.
	 *
	 * @return integer the length of the current string
	 */
	public function strlen() {
		return strlen($this->_value);
	}

	/**
	 * Maps to the standard PHP "substr" function.
	 *
	 * @param integer $start the starting point (negative steps back from the end)
	 * @param integer $length the number of characters to return (negative steps back from the starting point)
	 * @return string the resulting string
	 */
	public function substr($start = 0, $length = 0) {
		return substr($this->_value, $start, $length);
	}

	/**
	 * Maps to the standard PHP "trim" function.
	 *
	 * @param string $charList a list of characters to strip
	 * @return string the resulting string
	 */
	public function trim($charList = null) {
		return trim($this->_value, $charList);
	}
}