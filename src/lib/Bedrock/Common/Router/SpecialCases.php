<?php
/**
 * Stores special cases for the router to handle. Useful when default route
 * parsing should not take place under specific circumstances.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 11/10/2008
 * @updated 11/10/2008
 */
class Bedrock_Common_Router_SpecialCases extends Bedrock {
	protected static $_cases = array();
	
	/**
	 * Adds the specified case to the list of special cases for the router to 
	 * handle.
	 *
	 * @param string $route the route to match
	 * @param string $controller the controller class to use
	 * @param string $method the controller method to call
	 */
	public static function add($route, $controller, $method) {
		if(!class_exists($controller)) {
			throw new Bedrock_Common_Router_Exception('The specified controller "' . $controller . '" could not be found.');
		}
		
		if(!method_exists($controller, $method)) {
			throw new Bedrock_Common_Router_Exception('The specified controller method "' . $controller . '::' . $method . '()" could not be found.');
		}
		
		self::$_cases[] = array(
			'route' => $route,
			'controller' => $controller,
			'method' => $method
		);
	}
	
	/**
	 * Retrieves all currently stored special cases.
	 *
	 * @return array an array containing all the stored special cases
	 */
	public static function retrieve() {
		return self::$_cases;
	}
}
?>