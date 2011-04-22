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
	Bedrock_Common::init('../cfg/application.xml');
	Bedrock_Common_Logger::logEntry();

	// Start Router
	Bedrock_Common_Registry::get('router')->delegate();

	Bedrock_Common_Logger::logExit();
}
catch(Bedrock_Common_Exception $ex) {
	Bedrock_Common_Logger::exception($ex);
	Bedrock_Common_Logger::logExit();
}
?>