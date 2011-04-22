<?php
/**
 * Session Handler
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 11/05/2008
 * @updated 11/05/2008
 */
class Bedrock_Common_Session extends Bedrock {
	protected static $_started = false;
	protected static $_destroyed = false;
	protected static $_id = NULL;
	protected static $_namespaces = array();
	
	/**
	 * Starts the session.
	 */
	public static function start() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if(!self::$_started) {
				session_start();
				self::$_started = true;
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('A problem was encountered while trying to start the session.');
		}
	}
	
	/**
	 * Returns whether or not the session has been started.
	 *
	 * @return boolean whether or not the session has been started
	 */
	public static function started() {
		return self::$_started;
	}
	
	/**
	 * Destroys the current session and all related data.
	 */
	public static function destroy() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if(!self::$_destroyed) {
				session_destroy();
				
				self::$_destroyed = true;
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('A problem was encountered while trying to destroy the session.');
		}
	}
	
	/**
	 * Returns whether or not the session has been destroyed.
	 *
	 * @return boolean whether or not the session has been destroyed
	 */
	public static function destroyed() {
		return self::$_destroyed;
	}
	
	/**
	 * Retrieves an instance of the specified namespace.
	 *
	 * @param string $namespace the namespace to retrieve
	 */
	public static function loadNamespace($namespace = NULL) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($namespace == NULL || trim($namespace) == '') {
				throw new Bedrock_Common_Session_Exception('Could not retrieve the specified namespace, the name was either invalid or empty.');
			}
			
			$namespace = trim($namespace);
			
			// Retrieve Namespace
			if(!isset(self::$_namespaces[$namespace])) {
				self::$_namespaces[$namespace] = new Bedrock_Common_Session_Namespace($namespace);
			}
			
			Bedrock_Common_Logger::logExit();
			return self::$_namespaces[$namespace];
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('The namespace "' . $namespace . '" could not be accessed.');
		}
	}
	
	/**
	 * Locks the specified namespace if it exists. If no namespace is specified,
	 * all namespaces will be locked.
	 *
	 * @param string $namespace the namespace to lock, leave blank to lock all
	 */
	public static function lock($namespace = NULL) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($namespace == NULL || trim($namespace) == '') {
				throw new Bedrock_Common_Session_Exception('Could not lock the specified namespace, the name was either invalid or empty.');
			}
			
			$namespace = trim($namespace);
			
			if(!isset(self::$_namespaces[$namespace])) {
				throw new Bedrock_Common_Session_Exception('The namespace "' . $namespace . '" could not be locked, no namespace by that name was found.');
			}
			
			self::$_namespaces[$namespace]->lock();
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('The namespace "' . $namespace . '" could not be locked.');
		}
	}
	
	/**
	 * Unlocks the specified namespace if it exists. If no namespace is
	 * specified, all namespaces will be unlocked.
	 *
	 * @param string $namespace the namespace to unlock, leave blank to unlock all
	 */
	public static function unlock($namespace = NULL) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($namespace == NULL || trim($namespace) == '') {
				throw new Bedrock_Common_Session_Exception('Could not unlock the specified namespace, the name was either invalid or empty.');
			}
			
			$namespace = trim($namespace);
			
			if(!isset(self::$_namespaces[$namespace])) {
				throw new Bedrock_Common_Session_Exception('The namespace "' . $namespace . '" could not be unlocked, no namespace by that name was found.');
			}
			
			self::$_namespaces[$namespace]->unlock();
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('The namespace "' . $namespace . '" could not be unlocked.');
		}
	}
	
	/**
	 * Clears the specified namespace of all values. If no namespace is
	 * specified, all namespaces are cleared.
	 *
	 * @param string $namespace the desired namespace to clear
	 */
	public function clear($namespace = NULL) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($namespace == NULL) {
				foreach(self::$_namespaces as $currentNamespace) {
					$currentNamespace->clear();
				}
			}
			else {
				self::loadNamespace($namespace)->clear();
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Sets or gets the current session ID. If an ID is specified, the session
	 * ID will be set to that value. If no ID is specified, the current ID will
	 * be returned.
	 *
	 * @param string $newSessionId a new session ID to use, leave blank to retrieve the current ID
	 */
	public static function id($newSessionId = NULL) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if(!self::started()) {
				throw new Bedrock_Common_Session_Exception('A session ID could not be retrieved, no session has been started.');
			}
			
			Bedrock_Common_Logger::logExit();
			return session_id($newSessionId);
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Session_Exception('The session ID could not be set and/or retrieved.');
		}
	}
}
?>