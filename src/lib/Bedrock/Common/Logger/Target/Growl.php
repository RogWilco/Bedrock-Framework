<?php
/**
 * Growl Logger Target
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 06/12/2008
 * @updated 06/12/2008
 */
class Bedrock_Common_Logger_Target_Growl extends Bedrock implements Bedrock_Common_Logger_Target_Interface {
	const LIMIT_CHAR = 400;
	const LIMIT_MSG = 3;

	protected $_growl = NULL;
	protected static $_count = 0;

	/**
	 * Default Constructor
	 *
	 * @param array $args the Growl initialization arguments
	 */
	public function __construct($args = array()) {
		parent::__construct();
		$this->open($args);
	}

	/**
	 * Opens a Growl notification stream using the specified parameters.
	 *
	 * @param array $args the Growl initialization arguments
	 */
	public function open($args = array()) {
		// Setup
		$address = $args[0];
		$password = $args[1];
		$appName = $args[2];
		
		// Initialize Growl Notification
		$this->_growl = new Bedrock_Common_Growl($address, $password, $appName);
		$this->_growl->addNotification('logger');
		$this->_growl->register();
	}

	/**
	 * Closes the current Growl notification stream.
	 */
	public function close() {
		$this->_growl = NULL;
	}

	/**
	 * Returns the format accepted by this Target.
	 *
	 * @return string the accepted input format
	 */
	public function getFormat() {
		return 'array';
	}

	/**
	 * Writes data to the output stream.
	 *
	 * @param array $data the data to write to the output stream
	 */
	public function write($data) {
		// Setup
		$message = $data[0];
		$type = $data[1];
		
		if(self::LIMIT_CHAR > 0) {
			$message = substr($message, 0, self::LIMIT_CHAR);
		}
		
		// Send Notification
		if(isset($this->_growl)) {
			if(self::$_count < self::LIMIT_MSG) {
				$this->_growl->notify('logger', $type, $message);
			}
			elseif(self::$_count == self::LIMIT_MSG) {
				$this->_growl->notify('logger', 'INFO', 'Message limit exceeded, one or more additional messages were received.');
			}

			self::$_count++;
		}
		else {
			throw new Bedrock_Common_Logger_Target_Exception('No Growl stream has been initialized.');
		}
	}
}
?>