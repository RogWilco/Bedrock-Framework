<?php
namespace Bedrock\Common\Alert;

/**
 * Alert interface for Alert objects.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 10/30/2008
 * @updated 07/02/2012
 */
interface AlertInterface {
	public static function alert($message, $title = '', $type = \Bedrock\Common\Alert::TYPE_BASE);
	public static function info($message, $title = '');
	public static function success($message, $title = '');
	public static function warn($message, $title = '');
	public static function error($message, $title = '');
}