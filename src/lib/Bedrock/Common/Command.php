<?php
namespace Bedrock\Common;

/**
 * Command Line Base Class
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 08/21/2008
 * @updated 07/02/2012
 */
class Command extends \Bedrock implements \Bedrock\Common\Command\CommandInterface {
	protected static function call($function, $params = array()) {
		$config = \Bedrock\Common\Registry::get('config');
		
		switch($config->system->os) {
			case 'darwin':
				\Bedrock\Common\Command\Darwin::$function();
				break;
				
			case 'linux':
				// \Bedrock\Common\Command\Linux::$$function();
				break;
				
			case 'win32':
				// \Bedrock\Common\Command\Win32::$$function();
				break;
		}
	}
	
	public static function exec($command, &$output = array()) {
		self::call('exec', array(&$output));
	}
	
	public static function reboot() {
		self::call('reboot');
	}
	
	public static function shutdown() {
		self::call('shutdown');
	}
}