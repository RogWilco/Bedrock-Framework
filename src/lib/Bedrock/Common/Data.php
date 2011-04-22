<?php
/**
 * Data Structure Base Class
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 04/20/2009
 * @updated 04/20/2009
 */
class Bedrock_Common_Data extends Bedrock implements ArrayAccess, Countable, SeekableIterator {
	protected $_value = null;
	protected $_data = array();
	protected $_options = array();
	private $_defaults = array(
		'convert_arrays' => false,
		'format_output' => true
	);

	/**
	 * Initializes a new Data object containing the specified data.
	 *
	 * @param mixed $data optional initial data
	 * @param boolean $convertArrays whether or not to convert supplied arrays to child Data objects
	 */
	public function __construct($data = array(), $convertArrays = false) {
		$this->optionReset();
		$this->optionSet('convert_arrays', (bool) $convertArrays);

		if(is_array($data) && count($data) > 0) {
			foreach($data as $name => $value) {
				if(is_array($value) && $this->_options['convert_arrays']) {
					$this->_data[$name] = new self($value, $this->_options['convert_arrays']);
				}
				else {
					$this->_data[$name] = $value;
				}
			}
		}
		elseif($data instanceof self && count($data) > 0) {
			foreach($data as $name => $value) {
				if($value instanceof self || (is_array($value) && $this->_options['convert_arrays'])) {
					$this->_data[$name] = new self($value, $this->_options['convert_arrays']);
				}
				else {
					$this->_data[$name] = $value;
				}
			}
		}
	}

	/**
	 * Sets the specified option to the specified value.
	 *
	 * @param string $name the name of the option to set
	 * @param mixed $value the value to apply
	 */
	public function optionSet($name, $value) {
		if(array_key_exists($name, $this->_options)) {
			$this->_options[$name] = $value;
		}
		else {
			throw new Bedrock_Common_Data_Exception('The specified option "' . $name . '" does not exist.');
		}
	}

	/**
	 * Retrieves the value for the specified option.
	 *
	 * @param string $name the name of the option to retrieve
	 * @return mixed the requested option's value
	 */
	public function optionGet($name) {
		if(array_key_exists($name, $this->_options)) {
			return $this->_options[$name];
		}
		else {
			throw new Bedrock_Common_Data_Exception('The specified option "' . $name . '" does not exist.');
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
		}
		elseif(array_key_exists($name, $this->_options)) {
			$this->_options[$name] = $this->_defaults[$name];
		}
		else {
			throw new Bedrock_Common_Data_Exception('The specified option "' . $name . '" does not exist.');
		}
	}

	/**
	 * Returns the currently stored data as an array.
	 *
	 * @return array the currently stored data
	 */
	public function toArray() {
		// Setup
		$result = array();

		foreach($this->_data as $name => $value) {
			if($value instanceof self) {
				$result[$name] = $value->toArray();
			}
			else {
				$result[$name] = $value;
			}
		}

		return $result;
	}

	/**
	 * Creates a clone of the current object.
	 *
	 * @return Bedrock_Common_Data the cloned object
	 */
	public function __clone() {
		return new self($this->_data, $this->optionGet('convert_arrays'));
	}

	/**
	 * Returns a string representation of the current object's data.
	 *
	 * @return string a string representation of the data
	 */
	public function __toString() {
		// Setup
		$result = get_class($this) . ' {' . "\n";
		
		foreach($this->_data as $name => $value) {
			if($value instanceof self) {
				$result .= (string) $value;
			}
			else {
				$result .= var_export($value, true);
			}
		}

		$result = '}';

		return $result;
	}

	/**
	 * Retrieves the specified value.
	 *
	 * @param string $name the name of the value to retrieve
	 * @return mixed the corresponding value
	 */
	public function __get($name) {
		return $this->offsetGet($name);
	}

	/**
	 * Sets the specified name to the specified value.
	 *
	 * @param string $name the name of the value to set
	 * @param mixed $value the value to apply
	 */
	public function __set($name, $value) {
		$this->offsetSet($name, $value);
	}

	/**
	 * Checks if the specified value is currently set.
	 * 
	 * @param string $name the name of the value to check
	 * @return boolean whether or not the value is set
	 */
	public function __isset($name) {
		return $this->offsetExists($name);
	}

	/**
	 * Unsets the specified value if set.
	 *
	 * @param string $name the name of the value to unset
	 */
	public function __unset($name) {
		$this->offsetUnset($name);
	}

	/**
	 * Checks if the specified offset is in use.
	 *
	 * @param mixed $offset the offset to check
	 * @return boolean whether or not the specified offset exists
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->_data);
	}

	/**
	 * Retrieves the element at the specified offset.
	 * 
	 * @param mixed $offset the offset to use
	 * @return mixed the corresponding element
	 */
	public function offsetGet($offset) {
		if(!$this->offsetExists($offset)) {
			$this->_data[$offset] = new self();
		}

		return $this->_data[$offset];
	}

	/**
	 * Sets the element at the specified offset to the specified value.
	 *
	 * @param mixed $offset the offset at which to set the value
	 * @param mixed $value the value to apply
	 */
	public function offsetSet($offset, $value) {
		$this->_data[$offset] = $value;
	}

	/**
	 * Unsets the element at the specified offset.
	 *
	 * @param mixed $offset the offset of the element to unset
	 */
	public function offsetUnset($offset) {
		unset($this->_data[$offset]);
	}

	/**
	 * Return the total number of stored elements.
	 *
	 * @return integer the total number of stored elements
	 */
	public function count() {
		return count($this->_data);
	}

	/**
	 * Return the current element.
	 * 
	 * @return mixed the element
	 */
	public function current() {
		return current($this->_data);
	}

	/**
	 * Return the key of the current element.
	 *
	 * @return integer the current element's key
	 */
	public function key() {
		return key($this->_data);
	}

	/**
	 * Move forward to the next element.
	 * 
	 * @return boolean returns TRUE on success, FALSE on failure
	 */
	public function next() {
		return next($this->_data);
	}

	/**
	 * Rewind to the first element.
	 *
	 * @return boolean returns TRUE on success, FALSE on failure
	 */
	public function rewind() {
		return reset($this->_data);
	}

	/**
	 * Seek to an absolute position.
	 * 
	 * @param integer $index position to seek to
	 */
	public function seek($index) {
		$this->rewind();
		$position = 0;

		while($position < $index && $this->valid()) {
			$this->next();
			$position++;
		}

		if(!$this->valid()) {
			throw new Bedrock_Common_Data_Exception('Invalid seek position.');
		}
	}

	/**
	 * Check if there is a current element after calls to rewind() or next().
	 *
	 * @return boolean whether or not the internal pointer points to a valid element
	 */
	public function valid() {
		return (current($this->_data) !== false);
	}
}
?>