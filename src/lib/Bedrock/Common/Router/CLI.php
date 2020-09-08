<?php
namespace Bedrock\Common\Router;

/**
 * A router that handles CLI commands, delegating to the proper CLI controller.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 09/07/2020
 * @updated 09/07/2020
 */
class CLI extends \Bedrock\Common\Router {
	protected $_argc = array();
	protected $_argv = 0;

	/**
	 * Initializes a new instance.
	 */
	public function __construct() {
		// Setup
		global $argc;
		global $argv;

		// Store argument count and values.
		$this->_argc = $argc;
		$this->_argv = $argv;

		// Exclude first argument (the invoked executable).
		$this->_argc--;
		array_shift($this->_argv);
	}

	public function delegate() {
		try {
			// Setup
			$class = '';
			$method = '';
			$args = array();

			// Find Controller


		}
		catch(\Bedrock\Common\Router\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			exit($ex->getCode());
		}
		catch(\Bedrock\Common\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			exit(1);
		}
	}
}