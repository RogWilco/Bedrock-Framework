<?php
namespace Bedrock\Common;

/**
 * Provides server stats and related information. This class currently only
 * works in a Mac OS X server evironment.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 07/21/2008
 * @updated 07/02/2012
 */
class Stats extends \Bedrock {
	/**
	 * Executes the specified command and returns the resulting output.
	 *
	 * @param string $command the command to execute
	 * @return string the last line of the command executed
	 */
	protected function execute($command, &$output = array()) {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$output = array();
			$returnVar = NULL;
			
			if($command != '') {
				\Bedrock\Common\Logger::info('Executing command "' . $command . '" at location "' . '' .'"');
				$result = exec($command, $output, $returnVar);
			}
			
			$result = explode(' up ', $result);
			$result = $result[1];
			
			$result = explode(' users', $result);
			$result = $result[0];
			
			$result = substr($result, 0, strrpos($result, ','));
			
			\Bedrock\Common\Logger::info('Returning result "' . $result . '"');
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}
	
	/**
	 * Retrieves the current server's uptime.
	 *
	 * @return string the current server's uptime
	 */
	public function getUptime() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$cmd = 'uptime';
			
			// Execute Command/Parse Results
			$result = self::execute($cmd);
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}
	
	/**
	 * Retrieves the current server's operating system.
	 *
	 * @return string the current server's operating system
	 */
	public function getOperatingSystem() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$cmd = 'sw_vers';
			
			// Execute Command/Parse Results
			self::execute($cmd, $output);
			
			$name = explode(':', $output[0]);
			$name = trim($name[1]);
			
			$version = explode(':', $output[1]);
			$version = trim($version[1]);
			
			$result = $name . ' (' . $version . ')';
			
			\Bedrock\Common\Logger::logExit();
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}
}