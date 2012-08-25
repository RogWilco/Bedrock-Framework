<?php
/**
 * PHPUnit Bootstrap Script
 *
 * Contains all code to be executed before running tests.
 *
 * @author Nick Williams
 * @version 1.0.0
 * @created 
 * @updated 
 */

spl_autoload_register(function($class) {
	include_once str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
});
