<?php
/**
 * Growl Notification Class
 * 
 * This class allows a PHP script to send notifications to a system running
 * the Mac OS X application Growl. This class was built from the Growl class
 * originally written by Tyler Hall, who really deserves the credit for this.
 * 
 * The original class can be found here: http://clickontyler.com/php-growl/
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 03/29/2008
 * @updated 03/29/2008
 */
class Bedrock_Common_Growl extends Bedrock {
	const GROWL_PRIORITY_LOW = -2;
	const GROWL_PRIORITY_MODERATE = -1;
	const GROWL_PRIORITY_NORMAL = 0;
	const GROWL_PRIORITY_HIGH = 1;
	const GROWL_PRIORITY_EMERGENCY = 2;
	
	protected $_appName = "PHP Growl";
	protected $_address;
	protected $_password;
	protected $_port = 9887;
	protected $_notifications = array();

	/**
	 * Initializes the growl class with the specified app name (if specified).
	 *
	 * @param string $address the IP address of the system running Growl
	 * @param string $password the password to use
	 * @param string $app_name a name to use when sending notifications to Growl
	 * @return void
	 */
	public function __construct($address, $password = '', $app_name = null) {
		$this->_address = $address;
		$this->_password = $password;
		
		if(isset($app_name))
			$this->_appName = utf8_encode($app_name);
			
		parent::__construct();
	}
	
	/**
	 * Sets the address and password for the system running Growl. Omit the
	 * password parameter if none is set.
	 *
	 * @param string $address the IP address of the system running Growl
	 * @param string $password the password required for access
	 * @return void
	 */
	public function setAddress($address, $password = '') {
		$this->_address = $address;
		$this->_password = $password;
	}
	
	/**
	 * Adds a notification type/category for notifications sent.
	 *
	 * @param string $name the name of the notification
	 * @param boolean $enabled whether or not the notification type is enabled
	 * @return void
	 */
	public function addNotification($name, $enabled = true) {
		if($name != "")
			$this->_notifications[] = array("name" => utf8_encode($name), "enabled" => $enabled);
	}
	
	/**
	 * Registers with the system running Growl.
	 *
	 * @param string $address a new address to use, otherwise the current class variable is used
	 * @param string $password a new password to use, otherwise the current class variable is used
	 * @return void
	 */
	public function register($address = null, $password = "") {
		if(isset($address)) {
			$this->_address = $address;
			$this->_password = $password;
		}
		
		$data = "";
		$defaults = "";
		$num_defaults = 0;
		
		for($i = 0; $i < count($this->_notifications); $i++)	{
			$data .= pack("n", strlen($this->_notifications[$i]["name"])) . $this->_notifications[$i]["name"];
			
			if($this->_notifications[$i]["enabled"])	{
				$defaults .= pack("c", $i);
				$num_defaults++;
			}
		}

		// pack(Protocol version, type, app name, number of notifications to register)
		$data  = pack("c2nc2", 1, 0, strlen($this->_appName), count($this->_notifications), $num_defaults) . $this->_appName . $data . $defaults;
		$data .= pack("H32", md5($data . $this->_password));

		$this->_send($data);
	}
	
	/**
	 * Sends a notification to the Growl application.
	 *
	 * @param string $name the name of the notification type (must be one of the types set with the addNotification() funciton).
	 * @param string $title the title of the notification
	 * @param string $message the message content for the notification
	 * @param integer $priority the priority level for the notification
	 * @param boolean $sticky determines whether or not the notification should be "sticky"
	 * @return void
	 */
	public function notify($name, $title, $message, $priority = 0, $sticky = false) {
		$name	 = utf8_encode($name);
		$title	= utf8_encode($title);
		$message  = utf8_encode($message);
		$priority = intval($priority);
		
		$flags = ($priority & 7) * 2;
		if($priority < 0) $flags |= 8;
		if($sticky) $flags |= 1;

		// pack(protocol version, type, priority/sticky flags, notification name length, title length, message length. app name length)
		$data = pack("c2n5", 1, 1, $flags, strlen($name), strlen($title), strlen($message), strlen($this->_appName));
		$data .= $name . $title . $message . $this->_appName;
		$data .= pack("H32", md5($data . $this->_password));

		$this->_send($data);
	}
	
	/**
	 * Sends the supplied notification data.
	 *
	 * @param object $data the data object holding the notification details
	 * @return void
	 */
	protected function _send($data) {
		if(function_exists("socket_create") && function_exists("socket_sendto")) {
			$sck = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
			socket_sendto($sck, $data, strlen($data), 0x100, $this->_address, $this->_port);
		}
		elseif(function_exists("fsockopen")) {
			$fp = fsockopen("udp://" . $this->_address, $this->_port);
			fwrite($fp, $data);
			fclose($fp);
		}
	}
}
?>