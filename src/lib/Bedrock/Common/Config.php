<?php
namespace Bedrock\Common;

/**
 * Configuration Object
 *
 * Allows for an object style interface to application configuration data.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 03/29/2008
 * @updated 07/02/2012
 */
class Config extends \Bedrock implements \ArrayAccess, \Countable, \SeekableIterator {
	protected $_data;
	protected $_locked;
	protected $_key;
	protected $_extends;
	protected $_section;

	/**
	 * Initializes a new configuration object.
	 *
	 * @param array $data the data to store
	 * @param boolean $locked whether or not the stored data should be writeable
	 * @param string $unlockKey if locked, this key passed to the unlock() method will unlock the object
	 */
	public function __construct($data = array(), $locked = true, &$unlockKey = '') {
		// Setup
		$this->_data = array();

		// Store Data
		foreach($data as $key => $value) {
			$this->__set($key, $value);
		}

		// Locked?
		if($locked) {
			$unlockKey = $this->lock();
		}
	}

	/**
	 * Clones the current object.
	 *
	 * @return mixed the cloned object
	 */
	public function __clone() {
		
	}

	/**
	 * Gets the specified value.
	 *
	 * @param string $name the name of the value to get
	 * @return mixed the requested value
	 */
	public function __get($name) {
		// Setup
		$result = null;

		// Retrieve Value
		if(array_key_exists($name, $this->_data)) {
			$result = $this->_data[$name];
		}

		return $result;
	}

	/**
	 * Sets the specified value.
	 *
	 * @param string $name the name of the value to set
	 * @param mixed $value the value to apply
	 */
	public function __set($name, $value) {
		if(!$this->_locked) {
			if(is_array($value)) {
				$this->_data[$name] = new self($value, $this->_locked);
			}
			else {
				$this->_data[$name] = $value;
			}
		}
		else {
			throw new \Bedrock\Common\Config\Exception('This Config object is locked (read-only).');
		}
	}

	/**
	 * Determines whether or not the specified value is set.
	 *
	 * @param string $name the name of the value to check
	 * @return boolean the result of the check
	 */
	public function __isset($name) {
		return isset($this->_data[$name]);
	}

	/**
	 * Unsets the specified value if set.
	 *
	 * @param string $name the name of the value to unset
	 */
	public function __unset($name) {
		if(!$this->_locked) {
			unset($this->_data[$name]);
		}
		else {
			throw new \Bedrock\Common\Config\Exception('This Config object is locked (read-only).');
		}
	}

	/**
	 * Returns a string representation of the Config object's contents.
	 *
	 * @return string a string representation of the object's contents
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Returns a string representation of the Config object's contents.
	 *
	 * @param string $prefix an optional prefix to prepend to each new line
	 * @return string a string representation of the object's contents
	 */
	public function toString($prefix = '') {
		// Setup
		$result = '';

		foreach($this->_data as $key => $value) {
			if($value instanceof \Bedrock\Common\Config) {
				$result .= $value->__toString($prefix . "\t");
			}
			else {
				$result .= $prefix . "\t" . $key . ': ' . $value . "\n";
			}
		}

		return $result;
	}

	/**
	 * Sets the config object to be read only.
	 *
	 * @return string a hash that can be used to later unock the object
	 */
	public function lock() {
		if(!$this->_locked) {
			$this->_locked = true;
			$this->_key = md5(time());
			
			return $this->_key;
		}
		else {
			throw new \Bedrock\Common\Config\Exception('This Config object is already locked.');
		}
	}

	/**
	 * Unlocks the Config object if it is currently locked.
	 *
	 * @param string $key the unlock key (returned by the Config::lock() method)
	 */
	public function unlock($key) {
		if($this->_locked) {
			if($key == $this->_key) {
				$this->_locked = false;
				$this->_key = '';
			}
			else {
				throw new \Bedrock\Common\Config\Exception('The specified unlock key was invalid.');
			}
		}
	}

	/**
	 * Merges another config object with the current object, overwriting
	 * existing data with the same name.
	 *
	 * @param \Bedrock\Common\Config $newConfig the Config object to merge
	 * @return \Bedrock\Common\Config the merged Config object
	 */
	public function merge($newConfig) {
		if(!($newConfig instanceof \Bedrock\Common\Config)) {
			throw new \Bedrock\Common\Config\Exception('Only valid Config objects can be merged.');
		}
		
		foreach($newConfig as $key => $config) {
			if(array_key_exists($key, $this->_data)) {
				if($config instanceof \Bedrock\Common\Config && $this->$key instanceof \Bedrock\Common\Config) {
					$this->$key = $this->$key->merge($config);
				} else {
					$this->$key = $config;
				}
			}
			else {
				$this->$key = $config;
			}
		}

		return $this;
	}

	/**
	 * Defined by the ArrayAccess interface.
	 *
	 * @param int $offset the value to check
	 * @return boolean whether or not the value exists
	 */
	public function offsetExists($offset) {
		$result = false;

		if($this->_data[$offset]) {
			$result = true;
		}

		return $result;
	}

	/**
	 * Defined by the ArrayAccess interface.
	 *
	 * @param integer $offset the specified value to retrieve
	 * @return mixed the corresponding value
	 */
	public function offsetGet($offset) {
		if(!$this->offsetExists($offset)) {
			throw new \Bedrock\Common\Config\Exception('A value does not exist at the requested offset of ' . $offset);
		}

		return $this->_data[$offset];
	}

	/**
	 * Defined by the ArrayAccess interface.
	 *
	 * @param integer $offset the offset to use
	 * @param mixed $value the value to add
	 */
	public function offsetSet($offset, $value) {
		if(!$this->_locked) {
			$this->_data[$offset] = $value;
		}
		else {
			throw new \Bedrock\Common\Config\Exception('This Config object is locked (read-only).');
		}
	}

	/**
	 * Defined by the ArrayAccess interface.
	 *
	 * @param integer $offset the offset to use
	 */
	public function offsetUnset($offset) {
		if(!$this->_locked) {
			unset($this->_data[$offset]);
		}
		else {
			throw new \Bedrock\Common\Config\Exception('This Config object is locked (read-only).');
		}
	}

    /**
     * Defined by the Countable interface.
     *
     * @return int
     */
    public function count() {
        return count($this->_data);
    }

    /**
     * Defined by the Iterator interface.
     *
     * @return mixed
     */
    public function current() {
        return current($this->_data);
    }

    /**
     * Defined by the Iterator interface.
     *
     * @return mixed
     */
    public function key() {
        return key($this->_data);
    }

    /**
     * Defined by the Iterator interface.
     */
    public function next() {
        next($this->_data);
    }

	/**
	 * Defined by the Iterator interface.
	 */
	public function prev() {
		prev($this->_data);
	}

    /**
     * Defined by the Iterator interface.
     *
     */
    public function rewind() {
        reset($this->_data);
    }

    /**
     * Defined by the Iterator interface.
     *
     * @return boolean
     */
    public function valid() {
        return (current($this->_data) !== false);
    }

	/**
	 * Defined by the SeekableIterator interface.
	 *
	 * @param string $index the index to seek
	 */
	public function seek($index) {
		// Setup
		$this->rewind();
		$position = 0;

		while($position < $index && $this->valid()) {
			$this->next();
			$position++;
		}

		if(!$this->valid()) {
			throw new \Bedrock\Common\Config\Exception('Invalid index specified.');
		}
	}
}