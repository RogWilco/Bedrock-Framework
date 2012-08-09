<?php
namespace Bedrock\Common;

/**
 * Displays unobtrusive alerts to the user.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 10/30/2008
 * @updated 07/02/2012
 */
class Alert extends \Bedrock implements \Bedrock\Common\Alert\AlertInterface {
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
		try {
			parent::__construct();
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Returns all alerts of the specified type, or all by default.
	 *
	 * @param string $name the name of the alert type to search for
	 * @return array the corresponding alerts
	 */
	public function __get($name) {
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
			
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
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
	 * @return \Bedrock\Common\Alert the resulting Alert object
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
	public static function alert($message, $title = '', $type = \Bedrock\Common\Alert::TYPE_BASE) {
		try {
			self::getInstance()->addAlert(array('message' => $message, 'title' => $title, 'type' => $type));
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
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