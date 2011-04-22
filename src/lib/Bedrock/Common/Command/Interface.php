<?php
/**
 * Command Line Interface
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 08/21/2008
 * @updated 08/21/2008
 */
interface Bedrock_Common_Command_Interface {
	public static function exec($command, &$output = array());
	public static function reboot();
	public static function shutdown();
}
?>