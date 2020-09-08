<?php
/**
 * Main Index Page
 * 
 * Initializes the application environment, setting up an autoload function,
 * include paths, and loads any needed configuration files.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.1
 * @created 04/08/2009
 * @updated 09/07/2020
 */

use Bedrock\Common;

// Imports
require_once '../lib/Bedrock.php';
require_once '../lib/Bedrock/Common.php';

try {
	// Initialize Environment
	Common::init('../cfg/application.xml');

	// Start Router
	Common\Registry::get('router')->delegate();
}
catch(Common\Exception $ex) {
	Common\Logger::exception($ex);
}
