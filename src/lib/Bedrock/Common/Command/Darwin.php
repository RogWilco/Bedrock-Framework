<?php
namespace Bedrock\Common\Command;

/**
 * Command Line: Darwin/Mac OS X
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 08/21/2008
 * @updated 07/02/2012
 */
class Darwin implements \Bedrock\Common\Command\CommandInterface {
	/**
	 * Attempts to execute the specified command.
	 *
	 * @param string $command the command to execute
	 * @return string the last line of output for the command executed
	 */
	public static function exec($command, &$output = array()) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$returnVar = NULL;
			
			if($command != '') {
				\Bedrock\Common\Logger::info('Executing command "' . $command . '" at location "' . getcwd() .'"');
				$result = exec($command, $output, $returnVar);
			}
			
			\Bedrock\Common\Logger::info('Returning result "' . $result . '"');
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}
	
	/**
	 * Reboots the system.
	 */
	public static function reboot() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			self::exec('sudo -S reboot < ../config/config.password.txt');
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}
	
	/**
	 * Shuts down the system.
	 */
	public static function shutdown() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			self::exec('sudo -S halt');
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}
}