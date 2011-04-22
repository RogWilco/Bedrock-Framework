<?php
/**
 * User Authentication Class
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 11/05/2008
 * @updated 11/05/2008
 */
class Bedrock_Common_Auth extends Bedrock {
	const STATUS_LOGGED_OUT = 0;
	const STATUS_LOGGED_IN = 1;
	
	const RESULT_SUCCESS = 1;
	const RESULT_FAILED = -1;
	const RESULT_FAILED_USERNAME = -2;
	const RESULT_FAILED_PASSWORD = -3;
	const RESULT_FAILED_BLOCKED = -4;
	const RESULT_FAILED_BANNED = -5;
	
	protected static $_status = self::STATUS_LOGGED_OUT;
	protected static $_protocol = 'Bedrock_Common_Auth_Protocol';
	protected static $_id = 0;
	
	/**
	 * Attempts to log a user in to the system.
	 *
	 * @param string $username the specified username
	 * @param string $password the specified password
	 * @return integer the result of the login process
	 */
	public static function login($username, $password) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$protocol = new self::$_protocol($username, $password);
			$result = $protocol->authenticate();

			if($result > 0) {
				self::$_id = Bedrock_Common_String::random(32);
				self::$_status = self::STATUS_LOGGED_IN;
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Auth_Exception('A problem was encountered while attempting to authenticate the specified user.');
		}
	}
	
	/**
	 * Logs the current user out of the system.
	 */
	public static function logout() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			self::$_status = self::STATUS_LOGGED_OUT;
			self::$_id = 0;
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Auth_Exception('The current user could not be logged out.');
		}
	}
	
	/**
	 * Returns the unique identifier for the currently logged in user.
	 *
	 * @return mixed the unique identifier for the current user, or 0 when not logged in
	 */
	public static function id() {
		return self::$_id;
	}
	
	/**
	 * Sets the protocol to use for authentication.
	 *
	 * @param string $protocol a valid authentication protocol
	 */
	public static function setProtocol($protocol) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			Bedrock_Common_Logger::info('Protocol set to: ' . $protocol);
			self::$_protocol = $protocol;
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Auth_Exception('The protocol could not be set.');
		}
	}
}
?>