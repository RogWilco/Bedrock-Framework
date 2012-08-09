<?php
namespace Bedrock;

/**
 * Common Utilities
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 03/13/2009
 * @updated 07/02/2012
 */
class Common extends \Bedrock {
	// Miscellaneous
	const HASH_SALT_LENGTH = 24;	// DO NOT CHANGE in a deployed environment!

	// Text Symbols
	const TXT_NEWLINE = "\n";
	const TXT_TAB = "\t";

	// Delimiter Symbols
	const DELIM_WIN = ';';
	const DELIM_UNIX = ':';

	/**
	 * Initializes the application environment.
	 *
	 * @param string $configPath absolute path to the application's main configuration file
	 * @param string $env the configuration environment to use
	 * @param string $callback an optional callback function to execute uplon completion
	 * @param boolean $session whether or not to start a session
	 */
	public static function init($configPath = '', $env = 'main', $callback = '', $session = true) {
		// Load Configuration
		require_once 'Common/Config.php';
		require_once 'Common/Config/Xml.php';

		if(!is_file($configPath)) {
			header('Location: install/');
		}

		$config = new \Bedrock\Common\Config\Xml($configPath, $env);

		// Setup
		define('ROOT', $config->root->system);

		switch($config->env->os) {
			default:
			case 'unix':
				define('DELIMITER', self::DELIM_UNIX);
				break;

			case 'windows':
				define('DELIMITER', self::DELIM_WIN);
				break;
		}
		
		// Autoload Function
		function __autoload($className) {
			// Imports
			require_once ROOT . '/lib/Bedrock.php';
			require_once ROOT . '/lib/Bedrock/Common.php';
			\Bedrock\Common::autoload($className, ROOT);
		}

		// Register Configuration
		\Bedrock\Common\Registry::set('config', $config);

		// Include Paths
		ini_set('include_path', ini_get('include_path') . DELIMITER .
			ROOT . DELIMITER .
			$config->root->cfg . DELIMITER .
			$config->root->lib . DELIMITER .
			$config->root->pub);

		// Error Reporting/Handling
		eval('error_reporting(' . $config->env->error . ');');
		set_error_handler('\\Bedrock\\Common::error', E_ALL - E_NOTICE);

		// Initialize: Logger
		\Bedrock\Common\Logger::init(\Bedrock\Common\Logger::LEVEL_INFO, $config->meta->title, $config->meta->version->application, $config->meta->status);

		// Add built-in targets that are enabled...
		if($config->logger->targets->system->active) {
			\Bedrock\Common\Logger::addTarget(new \Bedrock\Common\Logger\Target\System(), $config->logger->targets->system->level);
		}

		if($config->logger->targets->file->active) {
			\Bedrock\Common\Logger::addTarget(new \Bedrock\Common\Logger\Target\File($config->root->log . DIRECTORY_SEPARATOR . date('Y-m-d') . '.log'), $config->logger->targets->file->level);
		}

		if($config->logger->targets->firephp->active) {
			\Bedrock\Common\Logger::addTarget(new \Bedrock\Common\Logger\Target\FirePHP(), $config->logger->targets->firephp->level);
		}

		if($config->logger->targets->growl->active) {
			\Bedrock\Common\Logger::addTarget(new \Bedrock\Common\Logger\Target\Growl(array($config->growl->host, $config->growl->password, $config->meta->title)), $config->logger->targets->growl->level);
		}

		// Initialize: Session
		if($session) {
			\Bedrock\Common\Session::start();
		}

		$router = new \Bedrock\Common\Router();
		\Bedrock\Common\Registry::set('router', $router);

		// Execute Callback
		if($callback) {
			call_user_func($callback);
		}
	}

	/**
	 * An autoload function for classes that are part of the framework. It can
	 * either be used by itself iwthin an __autoload() definition, or can be
	 * implemented into an existing autoload function.
	 *
	 * @param string $className the name of the class to attempt to load
	 * @param string $classPath the path within which to search
	 */
	public static function autoload($className, $classPath = '') {
		// Setup
		$parts = explode('\\', $className);
		$filename = array_pop($parts) . '.php';
		$path = $classPath . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
		$file = $path . DIRECTORY_SEPARATOR . $filename;

		if(file_exists($file)) {
			require_once $file;

			if(!class_exists($className, false) && substr($filename, -13) != 'AlertInterface.php') {
				eval('class ' . $className . ' {}');
				throw new \Exception('Could not autoload class "' . $className . '" using path "' . $file . '", please check your configuration.');
			}
		}
		elseif(count($parts) > 2) {
			// Specific Path for Modules
			array_shift($parts);
			$isModule = array_shift($parts) == 'Module';
			$modulePath = $classPath . DIRECTORY_SEPARATOR . 'mod' . DIRECTORY_SEPARATOR . strtolower(array_shift($parts)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
			$moduleFile = $modulePath . DIRECTORY_SEPARATOR . $filename;

			if($isModule && file_exists($moduleFile)) {
				require_once $moduleFile;

				if(!class_exists($className, false) && substr($filename, -13) != 'AlertInterface.php') {
					eval('class ' . $className . ' {}');
					throw new \Exception('Could not autoload Module class "' . $className . '" using path "' . $moduleFile . '", please check your configuration.');
				}
			}
			else {
				throw new \Exception('Could not find class file for class "' . $className . '" using path "' . $file . '", please check your configuration.');
			}
		}
		else {
			throw new \Exception('Could not find class file for class "' . $className . '" using path "' . $file . '", please check your configuration.');
		}
	}

	/**
	 * An error handler for Bedrock based applications. It can be used to
	 * translate PHP errors into PHP Exceptions that can be caught, when used
	 * within a user-defined error handler.
	 *
	 * @param string $code the error code
	 * @param string $message the associated message
	 * @param string $file the file in which the error occurred
	 * @param integer $line the line on which the error occurred
	 */
	public static function error($code, $message, $file, $line) {
		require_once 'Bedrock/Common/Error/Exception.php';
		throw new \Bedrock\Common\Error\Exception($code, $message, $file, $line);
	}
}