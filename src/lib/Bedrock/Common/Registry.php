<?php
namespace Bedrock\Common;

/**
 * Application Registry Container
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.1
 * @created 03/29/2008
 * @updated 08/24/2012
 */
class Registry extends \Bedrock implements \ArrayAccess, \Countable, \SeekableIterator {
	protected static $_instance = null;
	protected $_registry = array();

	/**
	 * Initializes a Registry object.
	 */
	public function __construct() {
		// Setup
		$backtrace = debug_backtrace();

		if($backtrace[1]['function'] != '_init' || $backtrace[1]['class'] != 'Bedrock\\Common\\Registry') {
			throw new \Bedrock\Common\Registry\Exception('The registry implements the Singleton pattern, and cannot be instantiated outside the class.');
		}
	}

	/**
	 * Initializes a new Registry instance.
	 */
	protected static function _init() {
		self::$_instance = new self();
	}

	/**
	 * Retrieves the current instance of the registry. If a registry instance is
	 * passed as a parameter, the current instance (if set) will be replaced.
	 *
	 * @param Registry $instance a new Registry instance to store
	 *
	 * @throws Registry\Exception when the specified instance is not a valid Registry object
	 * @return Registry the current Registry instance
	 */
	public static function instance($instance = null) {
		// Retrieve  Current Instance
		if($instance === null) {
			if(self::$_instance === null) {
				self::_init();
			}
		}
		// Store New Instance
		else {
			if($instance instanceof \Bedrock\Common\Registry) {
				self::$_instance = $instance;
			}
			else {
				throw new \Bedrock\Common\Registry\Exception('The specified instance was not a valid Registry instance.');
			}
		}

		return self::$_instance;
	}

	/**
	 * Retrieves the entrie stored at the specified ID.
	 *
	 * @param string $id the ID of the entry to retrieve
	 *
	 * @throws Registry\Exception when an entry with the specified ID cannot be found
	 * @return mixed the corresponding entry
	 */
	public static function get($id) {
		// Setup
		$registry = self::instance();

		// Retrieve Entry
		if(!self::registered($id)) {
			throw new \Bedrock\Common\Registry\Exception('An entry with id "' . $id . '" was not found.');
		}

		return $registry->$id;
	}

	/**
	 * Registers the specified value using the specified ID.
	 *
	 * @param string $id the ID to register the entry with
	 * @param mixed $value the entry to register
	 * @param boolean $serializeable whether or not the entry can be serialized
	 */
	public static function set($id, $value, $serializeable = false) {
		// Setup
		$registry = self::instance();

		// Store Entry
		$registry->$id = $value;
	}

	/**
	 * Checks if an entry is registered with the specified ID.
	 *
	 * @param string $id the ID to check
	 *
	 * @return boolean whether or not an entry is registered
	 */
	public static function registered($id) {
		// Setup
		$registry = self::instance();

		return isset($registry->$id);
	}

	/**
	 * Clears the curreng registry of all values.
	 *
	 * @return void
	 */
	public static function clear() {
		self::$_instance = null;
	}

	/**
	 * Gets the specified value.
	 *
	 * @param string $id the ID of the value to get
	 *
	 * @return mixed the requested value
	 */
	public function __get($id) {
		// Setup
		$result = null;

		// Retrieve Value
		if(array_key_exists($id, $this->_registry)) {
			$result = $this->_registry[$id];
		}

		return $result;
	}

	/**
	 * Registers the specified value.
	 *
	 * @param string $id the ID of the value to register
	 * @param mixed $value the value to apply
	 */
	public function __set($id, $value) {
		$this->_registry[$id] = $value;
	}

	/**
	 * Determines whether or not the specified value is registered.
	 *
	 * @param string $id the ID of the value to check
	 * @return boolean the result of the check
	 */
	public function __isset($id) {
		return isset($this->_registry[$id]);
	}

	/**
	 * Removes the specified value if set.
	 *
	 * @param string $id the ID of the value to remove
	 */
	public function __unset($id) {
		unset($this->_registry[$id]);
	}

	/**
	 * Defined by the ArrayAccess interface.
	 *
	 * @param int $offset the value to check
	 *
	 * @return boolean whether or not the value exists
	 */
	public function offsetExists($offset) {
		$result = false;

		if($this->_registry[$offset]) {
			$result = true;
		}

		return $result;
	}

	/**
	 * Defined by the ArrayAccess interface.
	 *
	 * @param integer $offset the specified value to retrieve
	 *
	 * @throws Registry\Exception when the specified offset does not exist
	 * @return mixed the corresponding value
	 */
	public function offsetGet($offset) {
		if(!$this->offsetExists($offset)) {
			throw new \Bedrock\Common\Registry\Exception('A value does not exist at the requested offset of ' . $offset);
		}

		return $this->_registry[$offset];
	}

	/**
	 * Defined by the ArrayAccess interface.
	 *
	 * @param integer $offset the offset to use
	 * @param mixed $value the record to add
	 */
	public function offsetSet($offset, $value) {
		$this->_registry[$offset] = $value;
	}

	/**
	 * Defined by the ArrayAccess interface.
	 *
	 * @param integer $offset the offset to use
	 */
	public function offsetUnset($offset) {
		unset($this->_registry[$offset]);
	}

    /**
     * Defined by the Countable interface.
     *
     * @return int
     */
    public function count() {
        return count($this->_registry);
    }

    /**
     * Defined by the Iterator interface.
     *
     * @return mixed
     */
    public function current() {
        return current($this->_registry);
    }

    /**
     * Defined by the Iterator interface.
     *
     * @return mixed
     */
    public function key() {
        return key($this->_registry);
    }

    /**
     * Defined by the Iterator interface.
     */
    public function next() {
        next($this->_registry);
    }

	/**
	 * Defined by the Iterator interface.
	 */
	public function prev() {
		prev($this->_registry);
	}

    /**
     * Defined by the Iterator interface.
     *
     */
    public function rewind() {
        reset($this->_registry);
    }

    /**
     * Defined by the Iterator interface.
     *
     * @return boolean
     */
    public function valid() {
        return (current($this->_registry) !== false);
    }

	/**
	 * Defined by the SeekableIterator interface.
	 *
	 * @param string $index the index to seek
	 *
	 * @throws Registry\Exception when an invalid index is specified
	 * @return void
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
			throw new \Bedrock\Common\Registry\Exception('Invalid index specified.');
		}
	}
}