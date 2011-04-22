<?php
/**
 * Base plugin class upon which plugins are built.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 06/07/2009
 * @updated 06/07/2009
 */
abstract class Bedrock_Plugin extends Bedrock {
	protected $_properties = array();
	protected static $_defaults = array();
	protected static $_dependencies = array();

	/**
	 * Checks for all required dependencies for the plugin.
	 *
	 * @return boolean whether or not all dependency requirements have been met
	 */
	public static function checkDependencies() {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$result = true;

			if(count(self::missingDependencies()) > 0) {
				$result = false;
			}

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}

	/**
	 * Returns a list of required dependencies that could not be found for the
	 * current plugin.
	 *
	 * @return array an array of class names required for the current plugin
	 */
	public static function missingDependencies() {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$result = array();

			foreach(self::$_dependencies as $dependency) {
				if(!class_exists($dependency)) {
					$result[] = $dependency;
				}
			}

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}

	/**
	 * Attempts to load missing dependencies remotely using the specified
	 * source, or Bedrock's default package sources if a source is not
	 * specified.
	 *
	 * @param string $source a valid Bedrock package source with the required dependencies
	 * @return boolean whether or not the process was successful
	 */
	public static function loadDependencies($source = '') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = false;
			$config = Bedrock_Common_Registry::get('config');

			// Verify library directory is writeable.
			if(!is_writeable($config->root->lib)) {
				throw new Bedrock_Plugin_Exception('The target save directory "' . $config->root->lib . '" is not writeable, no external dependencies can be loaded.');
			}

			// Verify source is valid.

			// Download/save files.

			// Verify all dependencies were loaded.
			

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
