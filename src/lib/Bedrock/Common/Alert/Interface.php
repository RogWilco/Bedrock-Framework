<?php
/**
 * Alert interface for Alert objects.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 10/30/2008
 * @updated 10/30/2008
 */
interface Bedrock_Common_Alert_Interface {
	public static function alert($message, $title = '', $type = Bedrock_Common_Alert::TYPE_BASE);
	public static function info($message, $title = '');
	public static function success($message, $title = '');
	public static function warn($message, $title = '');
	public static function error($message, $title = '');
}
?>