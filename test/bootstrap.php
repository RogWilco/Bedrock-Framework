<?php
/**
 * PHPUnit Bootstrap Script
 * 
 * Contains all code to be executed before running tests.
 * 
 * @author Nick Williams
 * @version 1.0.1
 * @created 8/24/2012
 * @updated 08/24/2012
 */

spl_autoload_register(function($class) {
	include_once str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
});
