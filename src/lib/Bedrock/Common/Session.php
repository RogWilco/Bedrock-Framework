<?php
namespace Bedrock\Common;

/**
 * Session Handler
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 11/05/2008
 * @updated 07/02/2012
 */
class Session extends \Bedrock {
	protected static $_started = false;
	protected static $_destroyed = false;
	protected static $_id = NULL;
	protected static $_namespaces = array();
	
	/**
	 * Starts the session.
	 */
	public static function start() {
		try {
			if(!self::$_started) {
				session_start();
				self::$_started = true;
			}
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Session\Exception('A problem was encountered while trying to start the session.');
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
		try {
			if(!self::$_destroyed) {
				session_destroy();
				
				self::$_destroyed = true;
			}
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Session\Exception('A problem was encountered while trying to destroy the session.');
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
		try {
			if($namespace == NULL || trim($namespace) == '') {
				throw new \Bedrock\Common\Session\Exception('Could not retrieve the specified namespace, the name was either invalid or empty.');
			}
			
			$namespace = trim($namespace);
			
			// Retrieve Namespace
			if(!isset(self::$_namespaces[$namespace])) {
				self::$_namespaces[$namespace] = new \Bedrock\Common\Session\SessionNamespace($namespace);
			}
			
			return self::$_namespaces[$namespace];
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Session\Exception('The namespace "' . $namespace . '" could not be accessed.');
		}
	}
	
	/**
	 * Locks the specified namespace if it exists. If no namespace is specified,
	 * all namespaces will be locked.
	 *
	 * @param string $namespace the namespace to lock, leave blank to lock all
	 */
	public static function lock($namespace = NULL) {
		try {
			if($namespace == NULL || trim($namespace) == '') {
				throw new \Bedrock\Common\Session\Exception('Could not lock the specified namespace, the name was either invalid or empty.');
			}
			
			$namespace = trim($namespace);
			
			if(!isset(self::$_namespaces[$namespace])) {
				throw new \Bedrock\Common\Session\Exception('The namespace "' . $namespace . '" could not be locked, no namespace by that name was found.');
			}
			
			self::$_namespaces[$namespace]->lock();
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Session\Exception('The namespace "' . $namespace . '" could not be locked.');
		}
	}
	
	/**
	 * Unlocks the specified namespace if it exists. If no namespace is
	 * specified, all namespaces will be unlocked.
	 *
	 * @param string $namespace the namespace to unlock, leave blank to unlock all
	 */
	public static function unlock($namespace = NULL) {
		try {
			if($namespace == NULL || trim($namespace) == '') {
				throw new \Bedrock\Common\Session\Exception('Could not unlock the specified namespace, the name was either invalid or empty.');
			}
			
			$namespace = trim($namespace);
			
			if(!isset(self::$_namespaces[$namespace])) {
				throw new \Bedrock\Common\Session\Exception('The namespace "' . $namespace . '" could not be unlocked, no namespace by that name was found.');
			}
			
			self::$_namespaces[$namespace]->unlock();
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Session\Exception('The namespace "' . $namespace . '" could not be unlocked.');
		}
	}
	
	/**
	 * Clears the specified namespace of all values. If no namespace is
	 * specified, all namespaces are cleared.
	 *
	 * @param string $namespace the desired namespace to clear
	 */
	public function clear($namespace = NULL) {
		try {
			if($namespace == NULL) {
				foreach(self::$_namespaces as $currentNamespace) {
					$currentNamespace->clear();
				}
			}
			else {
				self::loadNamespace($namespace)->clear();
			}
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}

	/**
	 * Sets or gets the current session ID. If an ID is specified, the session
	 * ID will be set to that value. If no ID is specified, the current ID will
	 * be returned.
	 *
	 * @param string $newSessionId a new session ID to use, leave blank to retrieve the current ID
	 *
	 * @throws \Bedrock\Common\Session\Exception if a session ID cannot be set and/or retrieved
	 * @return string the current session ID
	 */
	public static function id($newSessionId = NULL) {
		try {
			if(!self::started()) {
				throw new \Bedrock\Common\Session\Exception('A session ID could not be retrieved, no session has been started.');
			}
			
			return session_id($newSessionId);
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Session\Exception('The session ID could not be set and/or retrieved.');
		}
	}
}