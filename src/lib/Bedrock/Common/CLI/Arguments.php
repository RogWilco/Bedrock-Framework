<?php
namespace Bedrock\Common\CLI;

/**
 * Represents a collection of command-line arguments.
 * 
 * @package Bedrock\CLI
 * @author Nick Williams
 * @version 1.0.0
 * @created 09/07/2020
 * @updated 09/07/2020
 */
class Arguments implements \ArrayAccess, \Countable {
	const TYPE_NAMED = 'named';
	const TYPE_SEQUENTIAL = 'sequential';
	const TYPE_SWITCHED = 'switched';

	protected $_named = array();
	protected $_sequential = array();
	protected $_switched = array();

	/**
	 * Initializes a new arguments container, parsing and storing the specified argument string.
	 *
	 * @param string $argString the arguments to be parsed
	 */
	public function __construct($argString = '') {
		// Parse Supplied Arguments
		$this->_parse($argString);
	}

	/**
	 * Parses the specified argument string into the relevant private containers for each type.
	 * @TODO: Add support for parsing arrays.
	 *
	 * @param string $argString the arguments to be parsed
	 *
	 * @return void
	 */
	protected function _parse($argString = '') {
		// Setup
		$chunks = array();
		preg_match_all('/\S*"(?:\\\\.|[^\\\\"])*"|\S+/', $argString, $chunks);
		$chunks = $chunks[0];

		if(count($chunks)) {
			foreach($chunks as $chunk) {
				if(substr($chunk, 0, 1) == '-') {
					if(strpos($chunk, '=') === false) {
						$parts = str_split(substr($chunk, 1));

						foreach($parts as $part) {
							$this->_switched[$part] = true;
						}
					}
					else {
						$parts = explode('=', substr($chunk, 1), 2);
						$this->_named[$parts[0]] = $this->_valueFromString($parts[1]);
					}
				}
				else {
					$this->_sequential[] = $this->_valueFromString($chunk);
				}
			}
		}
	}

	/**
	 * Checks if the specified key exists in the specified array.
	 *
	 * @param mixed $key the key with which to check
	 * @param array $array the array in which to search
	 *
	 * @return bool
	 */
	protected function _exists($key, $array) {
		return array_key_exists($key, $array);
	}

	/**
	 * Parses the specified string into a corresponding primitive value based
	 * on certain cues.
	 *
	 * @param string $value the string representation to be parsed
	 *
	 * @return mixed the resulting value
	 */
	protected function _valueFromString($value) {
		// Setup
		$result = null;

		if(is_numeric($value)) {
			if(strpos($value, '.') >= 0) {
				$result = (float) $value;
			}
			else {
				$result = (int) $value;
			}
		}
		elseif(strtolower($value) === 'true') {
			$result = true;
		}
		elseif(strtolower($value) === 'false') {
			$result = false;
		}
		elseif(preg_match('/^.*={0,1}\[(.|\\n)*\]$/', $value)) {
			$elements = array();
			preg_match_all('/,*(([^,]*={0,1}\\[(.|\\n)+\\])|([^,]+)),*/', substr($value, 1, -1), $elements);
			$elements = $elements[1];

			$result = array();

			foreach($elements as $element) {
				if(strpos($element, '=') !== false) {
					$parts = explode('=', $element, 2);
					$result[$parts[0]] = $this->_valueFromString($parts[1]);
				}
				else {
					$result[] = $this->_valueFromString($element);
				}
			}
		}
		else {
			if(preg_match('/^\"(.|\\n)*\"$/', $value)) {
				$result = preg_replace('/\\\([\\"])/', '$1', substr($value, 1, -1));
			}
			else {
				$result = preg_replace('/\\\([\\"\\\])/', '$1', $value);
			}
		}

		return $result;
	}

	/**
	 * Converts the specified value into a string valid for use as a command
	 * line argument.
	 *
	 * @param array|boolean|float|integer|string $value the value to be formatted
	 *
	 * @return string the formatted value
	 * @throws Exception if the specified value is of an unsupported type
	 */
	protected function _valueToString($value) {
		// Setup
		$result = null;

		switch(gettype($value)) {
			default:
				throw new Exception('Value of type "' . gettype($value) . '" cannot be converted for use as an argument.');
				break;

			case 'array':
				$values = array();

				foreach($value as $key => $val) {
					if(is_integer($key)) {
						$values[] = $this->_valueToString($val);
					}
					else {
						$values[] = $this->_valueToString($key) . '=' . $this->_valueToString($val);
					}
				}

				$result = '[' . implode($values, ',') . ']';
				break;

			case 'boolean':
				$result = ($value ? 'true' : 'false');
				break;

			case 'double':
			case 'float':
			case 'integer':
				$result = (string) $value;
				break;

			case 'string':
				$escapedCharPattern = '/([\"\\\])/';

				if(preg_match($escapedCharPattern, $value)) {
					$value = preg_replace($escapedCharPattern, '\\\$1', $value);
				}

				$whitespacePattern = '/\s/';

				if(preg_match($whitespacePattern, $value)) {
					$value = '"' . $value . '"';
				}

				$result = $value;
				break;
		}

		return $result;
	}

	/**
	 * Retrieves all arguments of the specified type.
	 *
	 * @param string $type the type of arguments to be retrieved
	 *
	 * @throws Exception if an invalid argument type is specified
	 * @return array the corresponding arguments
	 */
	public function __get($type) {
		switch($type) {
			default:
				throw new Exception('Invalid argument type "' . $type . '" specified.');
				break;

			case self::TYPE_NAMED:
				return $this->_named;
				break;

			case self::TYPE_SEQUENTIAL:
				return $this->_sequential;
				break;

			case self::TYPE_SWITCHED:
				return $this->_switched;
				break;
		}
	}

	/**
	 * Returns a string representation of the current arguments. Can be used
	 * in a relevant command line command.
	 *
	 * @return string the assembled argument string
	 */
	function __toString() {
		// Setup
		$result = '';

		try {
			if(count($this->_switched)) {
				$result .= '-';

				foreach($this->_switched as $key => $value) {
					if($value) {
						$result .= $key;
					}
				}
			}

			if(count($this->_named)) {
				foreach($this->_named as $key => $value) {
					$formattedKey = $this->_valueToString($key);
					$formattedValue = $this->_valueToString($value);

					if($formattedValue !== null) {
						$result .= ($result ? ' ' : '') . '-' . $formattedKey . '=' . $formattedValue;
					}
				}
			}

			if(count($this->_sequential)) {
				foreach($this->_sequential as $key => $value) {
					$formattedValue = $this->_valueToString($value);

					if($formattedValue !== null) {
						$result .= ($result ? ' ' : '') . $formattedValue;
					}
				}
			}
		}
		catch(Exception $ex) {
			return null;
		}

		return $result;
	}

	/**
	 * Clears all arguments currently being stored.
	 *
	 * @return void
	 */
	public function clear() {
		$this->_named = array();
		$this->_sequential = array();
		$this->_switched = array();
	}

	/**
	 * Returns the total number of arguments.
	 *
	 * @return int the total number of stored arguments
	 */
	public function count() {
		return count($this->_named) + count($this->_sequential) + count($this->_switched);
	}

	/**
	 * Checks if the specified offset exists.
	 *
	 * @param mixed $offset the offset for which to check
	 *
	 * @return bool whether or not the offset was found
	 */
	public function offsetExists($offset) {
		return $this->_exists($offset, $this->_named)
			|| $this->_exists($offset, $this->_sequential)
			|| $this->_exists($offset, $this->_switched);
	}

	/**
	 * Retrieves the value at the specified offset.
	 *
	 * @param mixed $offset the offset for the value to be retrieved
	 *
	 * @return mixed the corresponding value
	 * @throws Exception when the requested value cannot be found
	 */
	public function offsetGet($offset) {
		if(is_numeric($offset)) {
			if($this->_exists($offset, $this->_sequential)) {
				return $this->_sequential[$offset];
			}
			else {
				throw new Exception('The specified argument "' . $offset . '" was not found.');
			}
		}
		else {
			if($this->_exists($offset, $this->_named)) {
				return $this->_named[$offset];
			}
			elseif($this->_exists($offset, $this->_switched)) {
				return $this->_switched[$offset];
			}
			else {
				throw new Exception('The specified argument "' . $offset . '" was not found.');
			}
		}
	}

	/**
	 * Stores the specified value at the specified offset. Biased towards
	 * overwriting existing argument values. Otherwise a numeric offset will be
	 * stored as a sequential argument, and a string offset will be
	 * stored as a named argument.
	 *
	 * @param integer|string $offset the offset for which a value is to be stored
	 * @param mixed $value the value to be stored
	 *
	 * @return void
	 */
	public function offsetSet($offset, $value) {
		if(is_numeric($offset) && $this->_exists($offset, $this->_sequential)) {
			$this->_sequential[$offset] = $value;
		}
		elseif($this->_exists($offset, $this->_named)) {
			$this->_named[$offset] = $value;
		}
		elseif($this->_exists($offset, $this->_switched)) {
			$this->_switched[$offset] = $value;
		}
		elseif(is_numeric($offset)) {
			$this->_sequential[$offset] = $value;
		}
		else {
			$this->_named[$offset] = $value;
		}
	}

	/**
	 * Removes the value at the specified offset.
	 *
	 * @param integer|string $offset the offset referencing the value to be unset
	 *
	 * @return void
	 */
	public function offsetUnset($offset) {
		unset($this->_sequential[$offset]);
		unset($this->_named[$offset]);
		unset($this->_switched[$offset]);
	}
}
