<?php
/**
 * Main Index Page
 *
 * Initializes the application environment, setting up an autoload function,
 * include paths, and loads any needed configuration files.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 04/08/2009
 * @updated 04/08/2009
 */

// Imports
require_once '../lib/Bedrock.php';
require_once '../lib/Bedrock/Common.php';

try {
	// Initialize Environment
	\Bedrock\Common::init('../cfg/application.xml');
	\Bedrock\Common\Logger::logEntry();

	// Start Router
	\Bedrock\Common\Registry::get('router')->delegate();

	\Bedrock\Common\Logger::logExit();
}
catch(\Bedrock\Common\Exception $ex) {
	\Bedrock\Common\Logger::exception($ex);
	\Bedrock\Common\Logger::logExit();
}
?>