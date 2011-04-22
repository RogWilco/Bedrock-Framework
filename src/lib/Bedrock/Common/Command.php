<?php
/**
 * Command Line Base Class
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 08/21/2008
 * @updated 08/21/2008
 */
class Bedrock_Common_Command extends Bedrock implements Bedrock_Common_Command_Interface {
	protected static function call($function, $params = array()) {
		$config = Bedrock_Common_Registry::get('config');
		
		switch($config->system->os) {
			case 'darwin':
				Bedrock_Common_Command_Darwin::$function();
				break;
				
			case 'linux':
				// Bedrock_Common_Command_Linux::$$function();
				break;
				
			case 'win32':
				// Bedrock_Common_Command_Win32::$$function();
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
?>