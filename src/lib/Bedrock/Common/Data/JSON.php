<?php
namespace Bedrock\Common\Data;

/**
 * Data Container: JSON (JavaScript Object Notation)
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 04/23/2009
 * @updated 07/02/2012
 */
class JSON extends \Bedrock\Common\Data {
	/**
	 * Returns the stored data as a JSON string.
	 *
	 * @return string the formatted stored data
	 */
	public function __toString() {
		return self::encode($this->_data);
	}

	/**
	 * Encodes the specified data into a string.
	 *
	 * @param mixed $data the data to encode
	 *
	 * @throws \Bedrock\Common\Data\Exception when the specified data cannot be encoded
	 * @return string the encoded data
	 */
	public static function encode($data) {
		if(is_array($data)) {
			return json_encode($data);
		}
		elseif($data instanceof \Bedrock\Common\Data) {
			return json_encode($data->toArray());
		}
		else {
			throw new \Bedrock\Common\Data\Exception('The specified data is not in a supported format, please provide a valid array or Data descendant.');
		}
	}

	/**
	 * Decodes the specified string to an array.
	 *
	 * @param string $string the string to decode
	 *
	 * @throws \Bedrock\Common\Data\Exception when the specified data cannot be decoded
	 * @return array the decoded data
	 */
	public static function decode($string) {
		if(is_string($string)) {
			return json_decode($string);
		}
		else {
			throw new \Bedrock\Common\Data\Exception('Only strings can be decoded, please provide a valid string to decode.');
		}
	}

	/**
	 * Resets the specified option to its default value.
	 *
	 * @param string $name the name of the option to reset
	 */
	public function optionReset($name = '') {
		if($name == '') {
			$this->_options = array_merge($this->_options, $this->_defaults);
			parent::optionReset($name);
		}
		elseif(array_key_exists($name, $this->_defaults)) {
			$this->_options[$name] = $this->_defaults[$name];
		}
		else {
			parent::optionReset($name);
		}
	}
}