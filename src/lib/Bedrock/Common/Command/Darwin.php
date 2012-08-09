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
		try {
			// Setup
			$result = '';
			$returnVar = NULL;
			
			if($command != '') {
				\Bedrock\Common\Logger::info('Executing command "' . $command . '" at location "' . getcwd() .'"');
				$result = exec($command, $output, $returnVar);
			}
			
			\Bedrock\Common\Logger::info('Returning result "' . $result . '"');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Reboots the system.
	 */
	public static function reboot() {
		try {
			self::exec('sudo -S reboot < ../config/config.password.txt');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Shuts down the system.
	 */
	public static function shutdown() {
		try {
			self::exec('sudo -S halt');
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
}