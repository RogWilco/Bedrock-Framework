<?php
namespace Bedrock\Common;

/**
 * Defines a base router object that handles requests and loads the proper controller.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.1
 * @created 
 * @updated 09/07/2020
 */
abstract class Router extends \Bedrock {
	abstract public function delegate();
}