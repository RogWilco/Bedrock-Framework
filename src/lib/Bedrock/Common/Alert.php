<?php
/**
 * Displays unobtrusive alerts to the user.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 10/30/2008
 * @updated 10/30/2008
 */
class Bedrock_Common_Alert extends Bedrock implements Bedrock_Common_Alert_Interface {
	const TYPE_BASE = 'base';
	const TYPE_INFO = 'info';
	const TYPE_SUCCESS = 'success';
	const TYPE_WARN = 'warn';
	const TYPE_ERROR = 'error';
	
	protected static $_instance = NULL;
	protected $_alerts = array();
	
	/**
	 * Initializes the view object. This particular class should not be
	 * instantiated directly and should insted be used through its static
	 * methods.
	 */
	public function __construct() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			parent::__construct();
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Returns all alerts of the specified type, or all by default.
	 *
	 * @param string $name the name of the alert type to search for
	 * @return array the corresponding alerts
	 */
	public function __get($name) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = array();
			
			switch($name) {
				default:
				case 'all':
					$result = $this->_alerts;
					break;
					
				case self::TYPE_BASE:
					foreach($this->_alerts as $alert) {
						if($alert['type'] == self::TYPE_BASE) {
							$result[] = $alert;
						}
					}
					break;
					
				case self::TYPE_INFO:
					foreach($this->_alerts as $alert) {
						if($alert['type'] == self::TYPE_INFO) {
							$result[] = $alert;
						}
					}
					break;
					
				case self::TYPE_SUCCESS:
					foreach($this->_alerts as $alert) {
						if($alert['type'] == self::TYPE_SUCCESS) {
							$result[] = $alert;
						}
					}
					break;
					
				case self::TYPE_WARN:
					foreach($this->_alerts as $alert) {
						if($alert['type'] == self::TYPE_WARN) {
							$result[] = $alert;
						}
					}
					break;
					
				case self::TYPE_ERROR:
					foreach($this->_alerts as $alert) {
						if($alert['type'] == self::TYPE_ERROR) {
							$result[] = $alert;
						}
					}
					break;
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Adds an alert to the queue.
	 *
	 * @param array $alert an array containing an alert and its properties
	 */
	public function addAlert($alert) {
		$this->_alerts[] = $alert;
	}
	
	/**
	 * Returns the current instance of the alert object. If one does not exist,
	 * it will be created.
	 *
	 * @return Payload_View_Common_Alert the resulting Alert object
	 */
	public static function getInstance() {
		if(!self::$_instance) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Sends an alert to the user.
	 *
	 * @param string $message a message for the alert
	 * @param string $title an optional title for the alert
	 * @param string $type the type of alert to send
	 */
	public static function alert($message, $title = '', $type = Bedrock_Common_Alert::TYPE_BASE) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			self::getInstance()->addAlert(array('message' => $message, 'title' => $title, 'type' => $type));
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Sends an info alert to the user.
	 *
	 * @param string $message a message for the alert
	 * @param string $title an optional title for the alert
	 */
	public static function info($message, $title = '') {
		self::alert($message, $title, self::TYPE_INFO);
	}
	
	/**
	 * Sends a success alert to the user.
	 *
	 * @param string $message a message for the alert
	 * @param string $title an optional title for the alert
	 */
	public static function success($message, $title = '') {
		self::alert($message, $title, self::TYPE_SUCCESS);
	}
	
	/**
	 * Sends a warn alert to the user.
	 *
	 * @param string $message a message for the alert
	 * @param string $title an optional title for the alert
	 */
	public static function warn($message, $title = '') {
		self::alert($message, $title, self::TYPE_WARN);
	}
	
	/**
	 * Sends an error alert to the user.
	 *
	 * @param string $message a message for the alert
	 * @param string $title an optional title for the alert
	 */
	public static function error($message, $title = '') {
		self::alert($message, $title, self::TYPE_ERROR);
	}
}
?>