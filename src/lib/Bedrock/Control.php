<?php
namespace Bedrock;

/**
 * The base controller object.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 07/09/2008
 * @updated 07/02/2012
 */
abstract class Control extends \Bedrock {
	const ERROR_TYPE_UNKNOWN = 'UNKNOWN';
	const ERROR_TYPE_HTTP404 = 'HTTP404';

	/**
	 * Redirects the client to the specified location.
	 *
	 * @param string $location the URL to which the client should be sent
	 */
	public static function redirect($location) {
		\Bedrock\Common\Logger::info('Redirecting: ' . $location);
		header('Location: ' . $location);
	}
	
	/**
	 * Determines whether the current page request is a Bedrock-based AJAX
	 * request.
	 *
	 * @return boolean whether or not the request is an AJAX request
	 */
	public static function isAjaxRequest() {
		// Setup
		$result = false;
		
		if($_POST['ajax'] == 1) {
			$result = true;
		}
		
		return $result;
	}
	
	abstract function index($args);
}