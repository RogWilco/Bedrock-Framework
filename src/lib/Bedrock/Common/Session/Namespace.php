<?php
/**
 * Session Namespace Object
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 10/05/2008
 * @updated 10/05/2008
 */
class Bedrock_Common_Session_Namespace extends Bedrock {
	protected $_namespace = 'Global';
	protected $_locked = false;
	protected $_root = 'Bedrock';
	
	/**
	 * Initializes a new session namespace.
	 *
	 * @param string $namespace a name for the namespace
	 */
	public function __construct($namespace) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if(trim($namespace) === '') {
				throw new Bedrock_Common_Session_Exception('A namespace could not be initialized, empty or invalid namespace parameter specified.');
			}
			
			$this->_namespace = trim($namespace);
			
			if(!isset($_SESSION[$this->_root][$this->_namespace])) {
				$_SESSION[$this->_root][$this->_namespace] = array();
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('The namespace "' . $namespace . '" could not be created.');
		}
	}
	
	/**
	 * Saves the specified value to the current session namespace.
	 *
	 * @param string $name a name to associate with the value
	 * @param mixed $value the value to save
	 */
	public function __set($name, $value) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			$name = trim($name);
			
			if($name == '') {
				throw new Bedrock_Common_Session_Exception('The specified value could not be set, the name provided was an empty string.');
			}
			
			if(is_object($value)) {
				$value = array('serialized' => true, 'value' => serialize($value));
			}
			else {
				$value = array('serialized' => false, 'value' => $value);
			}
			
			$_SESSION[$this->_root][$this->_namespace][$name] = $value;
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('The value "' . $name . '" could not be set.');
		}
	}
	
	/**
	 * Retrieves the specified value from the current session namespace.
	 *
	 * @param string $name the name of the value to retrieve
	 * @return mixed the requested value
	 */
	public function __get($name) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			$name = trim($name);
			
			if($name == '') {
				throw new Bedrock_Common_Session_Exception('The specified value could not be retrieved, the name provided was an empty string.');
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
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('Could not retrieve the specfied value "' . $name . '"');
		}
	}
	
	/**
	 * Checks if the specified value is set.
	 *
	 * @param string $name the name of the value to check
	 */
	public function __isset($name) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = false;
			
			if(is_array($_SESSION[$this->_root][$this->_namespace][$name])
					&& array_key_exists('serialized', $_SESSION[$this->_root][$this->_namespace][$name])
					&& array_key_exists('value', $_SESSION[$this->_root][$this->_namespace][$name])) {
				$result = true;	
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('Could not determine if the value "' . $name . '" was set.');
		}
	}
	
	/**
	 * Unsets the specified value.
	 * 
	 * @param string $name the name of the value to check
	 */
	public function __unset($name) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			$name = trim($name);
			
			if($this->__isset($name)) {
				unset($_SESSION[$this->_root][$this->_namespace][$name]);
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('The value "' . $name . '" could not be unset.');
		}
	}
	
	/**
	 * Locks the current namespace and prevents any additional changes from
	 * being made.
	 */
	public function lock() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($this->_namespace == 'Global') {
				throw new Bedrock_Common_Session_Exception('The global namespace cannot be locked.');
			}
			
			$this->_locked = true;
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('The namespace "' . $this->_namespace . '" could not be locked.');
		}
	}
	
	/**
	 * Unlocks the current namespace, allowing for changes to be made.
	 */
	public function unlock() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			$this->_locked = false;
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Clears the contents of the current namespace.
	 */
	public function clear() {
		$_SESSION[$this->_root][$this->_namespace] = array();
	}
}
?>