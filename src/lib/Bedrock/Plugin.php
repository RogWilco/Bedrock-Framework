<?php
namespace Bedrock;

/**
 * Base plugin class upon which plugins are built.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 06/07/2009
 * @updated 07/02/2012
 */
abstract class Plugin extends \Bedrock {
	protected $_properties = array();
	protected static $_defaults = array();
	protected static $_dependencies = array();

	/**
	 * Checks for all required dependencies for the plugin.
	 *
	 * @return boolean whether or not all dependency requirements have been met
	 */
	public static function checkDependencies() {
		try {
			// Setup
			$result = true;

			if(count(self::missingDependencies()) > 0) {
				$result = false;
			}

			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}

	/**
	 * Returns a list of required dependencies that could not be found for the
	 * current plugin.
	 *
	 * @return array an array of class names required for the current plugin
	 */
	public static function missingDependencies() {
		try {
			// Setup
			$result = array();

			foreach(self::$_dependencies as $dependency) {
				if(!class_exists($dependency)) {
					$result[] = $dependency;
				}
			}

			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
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
		try {
			// Setup
			$result = false;
			$config = \Bedrock\Common\Registry::get('config');

			// Verify library directory is writeable.
			if(!is_writeable($config->root->lib)) {
				throw new \Bedrock\Plugin\Exception('The target save directory "' . $config->root->lib . '" is not writeable, no external dependencies can be loaded.');
			}

			// Verify source is valid.

			// Download/save files.

			// Verify all dependencies were loaded.
			

			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
}