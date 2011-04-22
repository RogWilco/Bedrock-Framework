<?php
/**
 * Interface for authentication protocols. This interface must be implemented by
 * any object used as an authentication protocol.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 11/07/2008
 * @updated 11/07/2008
 */
interface Bedrock_Common_Auth_Protocol_Interface {
	public function __construct($username, $password);
	public function authenticate();
	public function authResponse();
	public function authSuccess();
	public function authFail();
}
?>