<?php
namespace Bedrock\Common\Data;

/**
 * Data Container: YAML (YAML Ain't Markup Language)
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 04/23/2009
 * @updated 07/02/2012
 */
class YAML extends \Bedrock\Common\Data {
	const INDENT_CHAR = '    ';

	/**
	 * Initializes a new YAML data object.
	 * 
	 * @param mixed $data optional initial data
	 * @param boolean $convertArrays whether or not to convert supplied arrays to child Data objects
	 */
	public function __construct($data = null, $convertArrays = false) {
		if(is_string($data)) {
			$data = self::decode($data);
		}

		if(is_array($data)) {
			parent::__construct($data, $convertArrays);
		}
		else {
			parent::__construct();
		}
	}

	private $_defaults = array(
		'strict_typing' => false
	);
	
	/**
	 * Returns the stored data as a YAML string.
	 *
	 * @return string the formatted stored data
	 */
	public function __toString() {
		try {
			return self::encode($this->_data, true, '', $this->optionGet('strict_typing'));
		}
		catch(\Bedrock\Common\Data\Exception $ex) {
			return '';
		}
	}

	/**
	 * Encodes the specified data into a string.
	 *
	 * @param mixed $data the data to encode
	 * @return string the encoded data
	 */
	public static function encode($data, $root = true, $prepend = '', $strictTyping = false) {
		\Bedrock\Common\Logger::logEntry();

		try {
			// Setup
			$result = '';
			$valueIndent = '';

			// Mark start of document.
			if($root) {
				$result = '---' . \Bedrock\Common::TXT_NEWLINE;
			}

			if($data instanceof \Bedrock\Common\Data) {
				$data = $data->toArray();
			}

			// Build YAML
			if(is_array($data) || $data instanceof \Bedrock\Common\Data) {
				$keys = array_keys($data);
				$length = 0;

				foreach($keys as $key) {
					if(strlen($key) > $length) {
						$length = strlen($key);
					}
				}

				$valueIndent = ''; // @todo finish this

				foreach($data as $name => $value) {
					if(is_numeric($name)) {
						// Type: List
						if(is_array($value) || $value instanceof \Bedrock\Common\Data) {
							$result .= substr($prepend, 0, -2) . '- ' . substr(self::encode($value, false, $prepend, $strictTyping), strlen($prepend)) . \Bedrock\Common::TXT_NEWLINE;
						}
						// Type: Hash Member
						else {
							$result .= self::formatValue($value, $strictTyping) . ', ';
							//$result .= substr($prepend, -2) . '- ' . $value . \Bedrock\Common::TXT_NEWLINE;
						}
					}
					else {
						// Type: Complex Value
						if(is_array($value) || $value instanceof \Bedrock\Common\Data) {
							$isHash = false;
							$hasArray = false;
							$isAssoc = false;

							foreach($value as $key => $item) {
								if(!is_numeric($key)) {
									$isAssoc = true;
								}

								if(is_array($item)) {
									$hasArray = true;
								}

								if(!$isAssoc && !$hasArray) {
									$isHash = true;
									break;
								}
							}

							// Type: Hash Container
							if($isHash) {
								$result .= $prepend . $name . ': ' . '[' . substr(self::encode($value, false, $prepend . self::INDENT_CHAR, $strictTyping), 0, -2) . ']' . \Bedrock\Common::TXT_NEWLINE;
							}
							// Type: List Container
							else {
								$result .= $prepend . $name . ': ' . \Bedrock\Common::TXT_NEWLINE . self::encode($value, false, $prepend . self::INDENT_CHAR, $strictTyping) . \Bedrock\Common::TXT_NEWLINE;
							}
						}
						// Type: Primitive Value
						else {
							$result .= $prepend . $name . ': ' . self::formatValue($value, $strictTyping) . \Bedrock\Common::TXT_NEWLINE;
						}
					}
				}
			}
			else {
				throw new \Bedrock\Common\Data\Exception('The specified data is not in a supported format, please provide a valid array or Data descendant.');
			}

			// Mark end of document.
			if($root) {
				$result = str_replace(\Bedrock\Common::TXT_NEWLINE . \Bedrock\Common::TXT_NEWLINE . \Bedrock\Common::TXT_NEWLINE, /*\Bedrock\Common::TXT_NEWLINE .*/ \Bedrock\Common::TXT_NEWLINE, $result);
				$result = str_replace(\Bedrock\Common::TXT_NEWLINE . \Bedrock\Common::TXT_NEWLINE, \Bedrock\Common::TXT_NEWLINE, $result);

				$result .= '...';
			}

			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Data\Exception('Could not encode the specified data into a string: ' . $ex->getTrace());
		}
	}

	/**
	 * Decodes the specified string to an array.
	 *
	 * @param string $string the string to decode
	 * @return array the decoded data
	 */
	public static function decode($string) {
		// Setup
		$result = array();
		
		if(is_string($string)) {
			$stringArray = explode(\Bedrock\Common::TXT_NEWLINE, $string);
			$result = self::parseYamlArray($stringArray);
		}
		else {
			throw new \Bedrock\Common\Data\Exception('Only strings can be decoded, please provide a valid string to decode.');
		}

		return $result;
	}

	/**
	 * Parses an array of lines extracted from a YAML string.
	 *
	 * @param array $yamlArray an array of lines representing a YAML document
	 * @param integer $currentLevel the current indentation level being parsed
	 * @return array the parsed YAML data
	 */
	protected static function parseYamlArray(&$yamlArray, $currentLevel = 0) {
		// Setup
		$result = array();
		$count = count($yamlArray);
		$isList = false;
		$index = 0;
		
		while($count > 0) {
			$line = array_shift($yamlArray);
			$properties = self::getLineProperties($line);
			
			if($properties['type'] == 'data') {
				if($properties['level'] == $currentLevel) {
					$line = trim($line);

					// Handle LIst Items
					if($properties['subtype'] == 'list_item') {
						if(!$isList) {
							// Flag as being in a list.
							$isList = true;
							$index = 0;
						}
						else {
							$index++;
						}

						$line = substr(trim($line), 2);
					}
					
					$line = explode(':', $line);
					$name = trim($line[0]);
					$value = trim($line[1]);

					if($isList) {
						if($value == '') {
							$result[$index][$name] = self::parseYamlArray($yamlArray, ++$properties['level']);

						}
						else {
							// Hash
							if(substr($value, 0, 1) == '[') {
								$value = substr(substr($value, 1), 0, -1);
								$value = explode($value);
								$value = array_map('trim', $value);
							}

							$result[$index][$name] = $value;
						}
					}
					else {
						if($value == '') {
							$result[$name] = self::parseYamlArray($yamlArray, ++$properties['level']);
						}
						else {
							// Hash
							if(substr($value, 0, 1) == '[') {
								$value = substr(substr($value, 1), 0, -1);
								$value = explode($value);
								$value = array_map('trim', $value);
							}

							$result[$name] = $value;
						}
					}
				}
				elseif($properties['level'] > $currentLevel) {
					array_unshift($yamlArray, $line);
					$result[$index] = self::parseYamlArray($yamlArray, $properties['level']);
				}
				else {
					array_unshift($yamlArray, $line);
					break;
				}
			}

			// Decrement Count
			$count--;
		}

		return $result;
	}

	/**
	 * Parses the specified line and reutnrs an array of properties for the
	 * parsed line.
	 *
	 * @param string $line the YAML line to parse
	 * @return array the line's properties
	 */
	protected static function getLineProperties($line) {
		// Setup
		$result = array(
			'type' => 'empty',
			'level' => 0,
			'subtype' => ''
		);
		
		// Determine Line Type
		if(trim($line) == '') {
			$result['type'] = 'empty';
		}
		elseif(substr($line, 0, 3) == '---') {
			$result['type'] = 'document_start';
		}
		elseif(substr($line, 0, 3) == '...') {
			$result['type'] = 'document_end';
		}
		else {
			$result['type'] = 'data';

			// Determine Indentation Level
			$result['level'] = substr_count($line, self::INDENT_CHAR);

			// Count list marker as a level indentation.
			if(substr(trim($line), 0, 2) == '- ') {
				$result['level']++;
				$result['subtype'] = 'list_item';
			}

			
		}

		return $result;
	}

	/**
	 * Explicitly formats the specified value for use in a YAML document.
	 * 
	 * @param mixed $value the value to format
	 * @param boolean $strictTyping whether or not to use strict data typing
	 * @return string the formatted value
	 */
	protected static function formatValue($value, $strictTyping = false) {
		// Setup
		$result = '';

		if($strictTyping) {
			switch(gettype($value)) {
				default:
				case 'integer':
					$result = $value;
					break;

				case 'string':
					$value = str_replace('\'', '\\\'', $value);
					$result = '\'' . $value . '\'';
					break;

				case 'boolean':
					$result = $value ? 'Yes' : 'No';
					break;

				case 'float':
					$result = '!!float ' . $value;
					break;
			}
		}
		else {
			$result = $value;
		}

		return $result;
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