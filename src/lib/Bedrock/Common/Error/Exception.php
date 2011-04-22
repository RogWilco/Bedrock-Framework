<?php
/**
 * PHP Error Exception
 * 
 * An exception designed to report on PHP errors.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 07/18/2008
 * @updated 07/18/2008
 */
class Bedrock_Common_Error_Exception extends Bedrock_Common_Exception {
	/**
	 * Default Constructor
	 * 
	 * @param string $code the error code to use
	 * @param string $message the error message
	 * @param string $file the name of the file
	 * @param integer $line the line on which the error occurred
	 * @param string $context the context in which the error occurred
	 */
	public function __construct($code, $message, $file, $line, $context = null) {
		parent::__construct($message, $code);
		$this->file = $file;
		$this->line = $line;
		$this->context = $context;
	}
}
?>