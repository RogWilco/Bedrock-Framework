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

use Bedrock\Common;

// Imports
require_once '../lib/Bedrock.php';
require_once '../lib/Bedrock/Common.php';

try {
	// Initialize Environment
	Common::init('../cfg/application.xml');
	Common\Logger::logEntry();

	// Start Router
	Common\Registry::get('router')->delegate();

	Common\Logger::logExit();
}
catch(Common\Exception $ex) {
	Common\Logger::exception($ex);
	Common\Logger::logExit();
}
