<?php
namespace Bedrock\Common;

/**
 * Provides basic RESTful utilities useful for use with various web APIs.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 05/06/2009
 * @updated 07/02/2012
 */
class Rest extends \Bedrock\Common {
	/**
	 * Sends a request using cURL.
	 *
	 * @param string $url the URL to connect to
	 * @param array $options any additional cURL options
	 * @return string any response received
	 */
	public static function curl($url, $options = array()) {
		try {
			// Setup
			$result = '';

			if(!function_exists('curl_init')) {
				throw new \Bedrock\Common\Rest\Exception('PHP\'s cURL library is not available and is required to make requests with cURL.');
			}

			$ch = curl_init();

			// Apply Options
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt_array($ch, $options);

			// Execute Request
			$result = curl_exec($ch);

			// Close Connection
			curl_close($ch);

			if($result === false) {
				throw new \Bedrock\Common\Rest\Exception('A cURL error has occurred: ' . curl_error($ch));
			}

			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Rest\Exception('The HTTP request was not successful.');
		}
	}

	/**
	 * Performs an HTTP request via GET using the specified URL and optional
	 * parameters (as key/value pairs).
	 *
	 * @param string $url the URL to connect to
	 * @param array $params any parameters to send with the request
	 * @param array $options additional options
	 * @return string the returned response
	 */
	public static function get($url, $params = array(), $options = array()) {
		try {
			// Setup
			$result = '';
			$paramString = '';

			// Build Parameter String
			foreach($params as $key => $value) {
				$paramString .= urlencode($key) . '=' . urlencode($value) . '&';
			}

			rtrim($paramString, '&');

			// Apply CURL Options
			$options[CURLOPT_HTTPGET] = count($params);
			$options[CURLOPT_RETURNTRANSFER] = true;
			
			// Execute Request
			$result = self::curl($url, $options);

			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Rest\Exception('The HTTP request (via GET) was not successful.');
		}
	}

	/**
	 * Performs an HTTP request via POST using the specified URL and optional
	 * parameters (as key/value pairs).
	 *
	 * @param string $url the URL to connect to
	 * @param array $params any parameters to send with the request
	 * @param array $options additional options
	 * @return string the returned response
	 */
	public static function post($url, $params = array(), $options = array()) {
		try {
			// Setup
			$result = '';
			$paramString = '';

			// Build Parameter String
			foreach($params as $key => $value) {
				$paramString .= urlencode($key) . '=' . urlencode($value) . '&';
			}

			$paramString = rtrim($paramString, '&');

			// Apply CURL Options
			$options[CURLOPT_POST] = count($params);
			$options[CURLOPT_POSTFIELDS] = $paramString;
			$options[CURLOPT_RETURNTRANSFER] = true;
			
			// Execute Request
			$result = self::curl($url, $options);

			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Rest\Exception('The HTTP request (via POST) was not successful.');
		}
	}

	/**
	 * Performs an HTTP request via PUT using the specified URL and optional
	 * parameters (as key/value pairs).
	 *
	 * @param string $url the URL to connect to
	 * @param array $params any parameters to send with the request
	 * @param array $options additional options
	 * @return string the returned response
	 */
	public static function put($url, $file, $options = array()) {
		try {
			// Setup
			$result = '';

			// Verify File
			if(!is_file($file)) {
				throw new \Bedrock\Common\Rest\Exception('The specified location "' . $file . '" does not reference a valid file.');
			}

			// Apply cURL Options
			$options[CURLOPT_PUT] = true;
			$options[CURLOPT_INFILESIZE] = sizeof($file);
			$options[CURLOPT_INFILE] = $file;
			$options[CURLOPT_RETURNTRANSFER] = true;

			$result = self::curl($url, $options);

			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Rest\Exception('The HTTP request (via PUT) was not successful.');
		}
	}
}