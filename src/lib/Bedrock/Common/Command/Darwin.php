<?php
/**
 * Command Line: Darwin/Mac OS X
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 08/21/2008
 * @updated 08/21/2008
 */
class Bedrock_Common_Command_Darwin implements Bedrock_Common_Command_Interface {
	/**
	 * Attempts to execute the specified command.
	 *
	 * @param string $command the command to execute
	 * @return the last line of output for the command executed
	 */
	public static function exec($command, &$output = array()) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$returnVar = NULL;
			
			if($command != '') {
				Bedrock_Common_Logger::info('Executing command "' . $command . '" at location "' . getcwd() .'"');
				$result = exec($command, $output, $returnVar);
			}
			
			Bedrock_Common_Logger::info('Returning result "' . $result . '"');
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Reboots the system.
	 */
	public static function reboot() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			self::exec('sudo -S reboot < ../config/config.password.txt');
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Shuts down the system.
	 */
	public static function shutdown() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			self::exec('sudo -S halt');
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
}
?>