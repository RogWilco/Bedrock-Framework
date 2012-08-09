<?php
namespace Bedrock\Common\Session;

/**
 * Session Namespace Object
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 10/05/2008
 * @updated 07/02/2012
 */
class SessionNamespace extends \Bedrock {
	protected $_namespace = 'Global';
	protected $_locked = false;
	protected $_root = 'Bedrock';
	
	/**
	 * Initializes a new session namespace.
	 *
	 * @param string $namespace a name for the namespace
	 */
	public function __construct($namespace) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			if(trim($namespace) === '') {
				throw new \Bedrock\Common\Session\Exception('A namespace could not be initialized, empty or invalid namespace parameter specified.');
			}
			
			$this->_namespace = trim($namespace);
			
			if(!isset($_SESSION[$this->_root][$this->_namespace])) {
				$_SESSION[$this->_root][$this->_namespace] = array();
			}
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Session\Exception('The namespace "' . $namespace . '" could not be created.');
		}
	}
	
	/**
	 * Saves the specified value to the current session namespace.
	 *
	 * @param string $name a name to associate with the value
	 * @param mixed $value the value to save
	 */
	public function __set($name, $value) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			$name = trim($name);
			
			if($name == '') {
				throw new \Bedrock\Common\Session\Exception('The specified value could not be set, the name provided was an empty string.');
			}
			
			if(is_object($value)) {
				$value = array('serialized' => true, 'value' => serialize($value));
			}
			else {
				$value = array('serialized' => false, 'value' => $value);
			}
			
			$_SESSION[$this->_root][$this->_namespace][$name] = $value;
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Session\Exception('The value "' . $name . '" could not be set.');
		}
	}
	
	/**
	 * Retrieves the specified value from the current session namespace.
	 *
	 * @param string $name the name of the value to retrieve
	 * @return mixed the requested value
	 */
	public function __get($name) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			$name = trim($name);
			
			if($name == '') {
				throw new \Bedrock\Common\Session\Exception('The specified value could not be retrieved, the name provided was an empty string.');
			}
			
			if($this->__isset($name)) {
				if($_SESSION[$this->_root][$this->_namespace][$name]['serialized']) {
					$result = unserialize($_SESSION[$this->_root][$this->_namespace][$name]['value']);
				}
				else {
					$result = $_SESSION[$this->_root][$this->_namespace][$name]['value'];
				}
			}
			else {
				$result = NULL;
			}
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Session\Exception('Could not retrieve the specfied value "' . $name . '"');
		}
	}

	/**
	 * Checks if the specified value is set.
	 *
	 * @param string $name the name of the value to check
	 *
	 * @throws \Bedrock\Common\Session\Exception if the check is unsuccessful
	 * @return bool whether or not the specified value is set
	 */
	public function __isset($name) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = false;
			
			if(is_array($_SESSION[$this->_root][$this->_namespace][$name])
					&& array_key_exists('serialized', $_SESSION[$this->_root][$this->_namespace][$name])
					&& array_key_exists('value', $_SESSION[$this->_root][$this->_namespace][$name])) {
				$result = true;	
			}
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Session\Exception('Could not determine if the value "' . $name . '" was set.');
		}
	}
	
	/**
	 * Unsets the specified value.
	 * 
	 * @param string $name the name of the value to check
	 */
	public function __unset($name) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			$name = trim($name);
			
			if($this->__isset($name)) {
				unset($_SESSION[$this->_root][$this->_namespace][$name]);
			}
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Session\Exception('The value "' . $name . '" could not be unset.');
		}
	}
	
	/**
	 * Locks the current namespace and prevents any additional changes from
	 * being made.
	 */
	public function lock() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			if($this->_namespace == 'Global') {
				throw new \Bedrock\Common\Session\Exception('The global namespace cannot be locked.');
			}
			
			$this->_locked = true;
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Session\Exception('The namespace "' . $this->_namespace . '" could not be locked.');
		}
	}
	
	/**
	 * Unlocks the current namespace, allowing for changes to be made.
	 */
	public function unlock() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			$this->_locked = false;
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}
	
	/**
	 * Clears the contents of the current namespace.
	 */
	public function clear() {
		$_SESSION[$this->_root][$this->_namespace] = array();
	}
}