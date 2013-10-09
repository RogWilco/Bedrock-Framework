<?php
/**
 * PHPUnit Skeleton Generator Bootstrap Script
 * 
 * Contains all code to be executed before running phpunit-skelgen.
 * 
 * @author Nick Williams
 * @version 1.0.1
 * @created 8/24/2012
 * @updated 08/24/2012
 */

// Load PHPUnit Config
$configPath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
$configFile = $configPath . 'phpunit.xml';

if(is_file($configFile)) {
	$config = simplexml_load_file($configFile);

	// Set Include Path
	$includePaths = explode(':', $config->php->includePath);

	foreach($includePaths as &$path) {
		if(substr($path, 0, 1) == '.') {
			$path = realpath($configPath . $path);
		}
	}

	$includePath = implode(':', $includePaths);

	ini_set('include_path', ini_get('include_path') . ':' . $includePath);

	// ini_set
	if($config->php->ini) {
		foreach($config->php->ini as $ini) {
			$attr = $ini->attributes();
			
			ini_set($attr['name'], $attr['value']);
		}
	}

	// define
	if($config->php->const) {
		foreach($config->php->const as $const) {
			$attr = $const->attributes();
			
			define($attr['name'], $attr['value']);
		}
	}

	// $_GLOBALS
	if($config->php->var) {
		foreach($config->php->var as $var) {
			$attr = $var->attributes();
			
			$_GLOBALS[$attr['name']] = $attr['value'];
		}
	}

	// $_ENV
	if($config->php->env) {
		foreach($config->php->env as $env) {
			$attr = $env->attributes();
			
			$_ENV[$attr['name']] = $attr['value'];
		}
	}

	// $_POST
	if($config->php->post) {
		foreach($config->php->post as $post) {
			$attr = $post->attributes();
			
			$_POST[$attr['name']] = $attr['value'];
		}
	}

	// $_GET
	if($config->php->get) {
		foreach($config->php->get as $get) {
			$attr = $get->attributes();
			
			$_GET[$attr['name']] = $attr['value'];
		}
	}

	// $_COOKIE
	if($config->php->cookie) {
		foreach($config->php->cookie as $cookie) {
			$attr = $cookie->attributes();
			
			$_COOKIE[$attr['name']] = $attr['value'];
		}
	}

	// $_SERVER
	if($config->php->server) {
		foreach($config->php->server as $server) {
			$attr = $server->attributes();
			
			$_SERVER[$attr['name']] = $attr['value'];
		}
	}

	// $_FILES
	if($config->php->files) {
		foreach($config->php->files as $files) {
			$attr = $files->attributes();
			
			$_FILES[$attr['name']] = $attr['value'];
		}
	}

	// $_REQUEST
	if($config->php->request) {
		foreach($config->php->request as $request) {
			$attr = $request->attributes();
			
			$_REQUEST[$attr['name']] = $attr['value'];
		}
	}
}

// Load Primary Bootstrap
include 'bootstrap.php';