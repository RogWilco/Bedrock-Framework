<?php
/**
 * Provides server stats and related information. This class currently only
 * works in a Mac OS X server evironment.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 07/21/2008
 * @updated 07/21/2008
 */
class Bedrock_Common_Stats extends Bedrock {
	/**
	 * Executes the specified command and returns the resulting output.
	 *
	 * @param string $command the command to execute
	 * @return string the last line of the command executed
	 */
	protected function execute($command, &$output = array()) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$output = array();
			$returnVar = NULL;
			
			if($command != '') {
				Bedrock_Common_Logger::info('Executing command "' . $command . '" at location "' . '' .'"');
				$result = exec($command, $output, $returnVar);
			}
			
			$result = explode(' up ', $result);
			$result = $result[1];
			
			$result = explode(' users', $result);
			$result = $result[0];
			
			$result = substr($result, 0, strrpos($result, ','));
			
			Bedrock_Common_Logger::info('Returning result "' . $result . '"');
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Retrieves the current server's uptime.
	 *
	 * @return string the current server's uptime
	 */
	public function getUptime() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$cmd = 'uptime';
			
			// Execute Command/Parse Results
			$result = self::execute($cmd);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Retrieves the current server's operating system.
	 *
	 * @return string the current server's operating system
	 */
	public function getOperatingSystem() {
		Bedrock_Common_Logger::logEntry();
		
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
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
}
?>