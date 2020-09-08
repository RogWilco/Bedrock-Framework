<?php
namespace Bedrock;

/**
 * The base controller object.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.1
 * @created 07/09/2008
 * @updated 09/07/2020
 */
abstract class Control extends \Bedrock {
	abstract public function index($args);
}