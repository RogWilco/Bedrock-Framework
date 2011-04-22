<?php
/**
 * File Output Target
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 06/12/2009
 * @updated 06/12/2009
 */
class Bedrock_Common_Logger_Target_File extends Bedrock implements Bedrock_Common_Logger_Target_Interface {
	protected $_handle = NULL;

	/**
	 * Creates a new file output stream using the specified parameters.
	 *
	 * @param string $args the path of the file to open
	 */
	public function __construct($args) {
		parent::__construct();
		$this->open($args);
	}

	/**
	 * Opens a file stream using the specified path.
	 *
	 * @param string $args the path of the file to open
	 */
	public function open($args = array()) {
		if(!$this->_handle = fopen($args, 'a')) {
			throw new Bedrock_Common_Logger_Target_Exception('Could not open the specified file: ' . $args);
		}
	}

	/**
	 * Closes the current file stream.
	 */
	public function close() {
		fclose($this->_handle);
	}

	/**
	 * Returns the format accepted by this Target.
	 *
	 * @return string the accepted input format
	 */
	public function getFormat() {
		return 'string';
	}

	/**
	 * Writes data to the file stream.
	 *
	 * @param string $data the data to write to the file stream
	 */
	public function write($data) {
		if(!$this->_handle) {
			throw new Bedrock_Common_Logger_Target_Exception('Cannot write to file target, no file selected.');
		}
		elseif(fwrite($this->_handle, $data) === false) {
			throw new Bedrock_Common_Logger_Target_Exception('There was a problem writing to the file target.');
		}
	}
}
?>