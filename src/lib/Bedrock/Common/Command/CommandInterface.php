<?php
namespace Bedrock\Common\Command;

/**
 * Command Line Interface
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 08/21/2008
 * @updated 07/02/2012
 */
interface CommandInterface {
	public static function exec($command, &$output = array());
	public static function reboot();
	public static function shutdown();
}