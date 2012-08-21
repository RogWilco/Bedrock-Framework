<?php
namespace Bedrock;

/**
 * Base view object, used to represent a page.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 07/09/2008
 * @updated 07/02/2012
 */
abstract class View extends \Bedrock {
	const MESSAGE_ALL = 0;
	const MESSAGE_INFO = 1;
	const MESSAGE_ERROR = 2;
	const MESSAGE_WARN = 3;
	const MESSAGE_SUCCESS = 4;

	protected $_root;
	protected $_webroot;
	protected $_imgroot;
	protected $_pageroot;
	protected $_values = array();
	protected $_messages = array();
	protected $_forms = array();
	protected static $_cssStylesheets = array();
	protected static $_javascriptTemplates = array();
	protected static $_javascriptLibraries = array();
	
	/**
	 * The default constructor.
	 * 
	 * @return \Bedrock\View
	 */
	public function __construct() {
		parent::__construct();
		$this->_webroot = $this->_config->root->web . 'templates/' . $this->_config->template . '/';
		$this->_imgroot = $this->_webroot . 'images/';
		$this->_pageroot = $this->_webroot . 'pages/';
		$this->_root = 'pub/templates/'.$this->_config->template.'/pages/';
	}
	
	/**
	 * Set the values to use when displaying the page.
	 * 
	 * @param string $name the name of the value
	 * @param mixed $value the value to use
	 * @param mixed $default a default value if the passed value is empty
	 * @return void
	 */
	public function setValue($name, $value, $default = '') {
		if(gettype($value) != 'object' ) {
			\Bedrock\Common\Logger::info('Value Set: ' . $name . ' = ' . $value);
		}
		else {
			\Bedrock\Common\Logger::info('Value Set: ' . $name . ' = Object');
		}
		
		if(!isset($value) || count($value) == 0) {
			if(is_array($value)) {
				$value = array();
			}
			else {
				$value = $default;
			}
		}
		
		$this->_values[$name] = $value;
	}
	
	/**
	 * Returns the requested value.
	 * 
	 * @param string $name the name of the value to return
	 * @return mixed the requested value
	 */
	public function getValue($name) {
		return $this->_values[$name];
	}
	
	/**
	 * Sets the specified value.
	 *
	 * @param string $name the name of the value
	 * @param mixed $value the value to store
	 */
	public function __set($name, $value) {
		$this->setValue($name, $value);
	}
	
	/**
	 * Returns the requested value.
	 *
	 * @param string $name the name of the value to return
	 * @return mixed the requested value
	 */
	public function __get($name) {
		return $this->getValue($name);
	}
	
	/**
	 * Displays the requested value.
	 * 
	 * @param string $name the name of the value to display
	 * @return void
	 */
	public function printValue($name) {
		print($this->_values[$name]);
	}
	
	/**
	 * Adds the specified form to the template, using the specified name.
	 *
	 * @param string $name the name for the form
	 * @param \Bedrock\Common\Form\Generator $form the form object to store
	 */
	public function setForm($name, $form) {
		\Bedrock\Common\Logger::info('Form Set: ' . $name);
		$this->_forms[$name] = $form;
	}
	
	/**
	 * Returns the requested form.
	 *
	 * @param string $name the name of the form object to retrieve
	 * @return \Bedrock\Common\Form\Generator the requested form object
	 */
	public function getForm($name) {
		return $this->_forms[$name];
	}
	
	/**
	 * Sets a message of the specified type, to be displayed when the page loads.
	 *
	 * @param integer $type the type of message to add
	 * @param string $message the message contents
	 * @return void
	 */
	public function setMessage($type, $message) {
		switch($type) {
			default:
			case self::MESSAGE_INFO:
				$this->_messages['info'][] = $message;
				break;
				
			case self::MESSAGE_ERROR:
				$this->_messages['error'][] = $message;
				break;
				
			case self::MESSAGE_WARN:
				$this->_messages['warn'][] = $message;
				break;
				
			case self::MESSAGE_SUCCESS:
				$this->_messages['success'][] = $message;
				break;
		}
	}
	
	/**
	 * Adds an array of errors to the template's message array.
	 *
	 * @param array $errors an error array
	 */
	public function setErrors($errors) {
		foreach($errors as $error) {
			$this->_messages['error'][] = $error['msg'];
		}
	}
	
	/**
	 * Retrieves all messages of the specified type, or the entire message array
	 * by default.
	 *
	 * @param integer $type the message type to retrieve
	 * @return array an array of messages corresponding to the specified type
	 */
	public function getMessages($type = self::MESSAGE_ALL) {
		switch($type) {
			default:
			case self::MESSAGE_ALL:
				$messages = $this->_messages;
				break;
				
			case self::MESSAGE_INFO:
				$messages = $this->_messages['info'];
				break;
				
			case self::MESSAGE_ERROR:
				$messages = $this->_messages['error'];
				break;
				
			case self::MESSAGE_WARN:
				$messages = $this->_messages['warn'];
				break;
				
			case self::MESSAGE_SUCCESS:
				$messages = $this->_messages['success'];
				break;
		}
		
		return $messages;
	}
	
	/**
	 * Determines if the current template has any messages of the specified type
	 * set. All message types are checked by default.
	 *
	 * @param integer $type the message type to check
	 * @return boolean whether or not any messages were found
	 */
	public function hasMessages($type = self::MESSAGE_ALL) {
		$result = false;
		
		switch($type) {
			default:
			case self::MESSAGE_ALL:
				if($this->hasMessages(self::MESSAGE_INFO) &&
						$this->hasMessages(self::MESSAGE_ERROR) &&
						$this->hasMessages(self::MESSAGE_WARN) &&
						$this->hasMessages(self::MESSAGE_SUCCESS)) {
					$result = true;
				}
				break;
				
			case self::MESSAGE_INFO:
				if(count($this->_messages['info']) > 0) {
					$result = true;
				}
				break;
				
			case self::MESSAGE_ERROR:
				if(count($this->_messages['error']) > 0) {
					$result = true;
				}
				break;
				
			case self::MESSAGE_WARN:
				if(count($this->_messages['warn']) > 0) {
					$result = true;
				}
				break;
				
			case self::MESSAGE_SUCCESS:
				if(count($this->_messages['success']) > 0) {
					$result = true;
				}
				break;
		}
		
		return $result;
	}
	
	/**
	 * Outputs the current template's root path.
	 */
	public function printRoot($which) {
		switch($which) {
			case 'web':
				echo $this->_webroot;
				break;
				
			case 'img':
				echo $this->_imgroot;
				break;
				
			case 'page':
				echo $this->_pageroot;
				break;
				
			default:
			case 'root':
				echo $this->_root;
				break;
		}
	}

    /**
     * Registers the specified CSS stylesheet.
     *
     * @param string $cssStylesheet the location of the CSS stylesheet
     * @throws View\Exception
     * @return void
     */
	public static function registerCssStylesheet($cssStylesheet) {
		if(!is_file('../' . $cssStylesheet)) {
			throw new \Bedrock\View\Exception('The specified stylesheet could not be found: "' . $cssStylesheet . '"');
		}

		if(!in_array($cssStylesheet, self::$_cssStylesheets)) {
			self::$_cssStylesheets[] = $cssStylesheet;
		}
	}

    /**
     * Registers the specified JavaScript template location.
     *
     * @param string $javascriptTemplate the location of the JS template file
     * @throws View\Exception
     * @return void
     */
	public static function registerJavascriptTemplate($javascriptTemplate) {
		if(!is_file('../' . $javascriptTemplate)) {
			throw new \Bedrock\View\Exception('The specified template file could not be found: "' . $javascriptTemplate . '"');
		}
		
		if(!in_array($javascriptTemplate, self::$_javascriptTemplates)) {
			self::$_javascriptTemplates[] = $javascriptTemplate;
		}
	}

    /**
     * Registers the specified JavaScript library location.
     *
     * @param string $javascriptLibrary the location of the JS library file
     * @throws View\Exception
     * @return void
     */
	public static function registerJavascriptLibrary($javascriptLibrary) {
		if(!is_file('.'.$javascriptLibrary)) {
			throw new \Bedrock\View\Exception('The specified library file could not be found: "' . $javascriptLibrary . '"');
		}
		
		if(!in_array($javascriptLibrary, self::$_javascriptLibraries)) {
			self::$_javascriptLibraries[] = $javascriptLibrary;
		}
	}
	
	/**
	 * Imports a collection of JavaScript template locations.
	 *
	 * @param array $javascriptTemplates a collection of JavaScript template locations
	 */
	public static function importJavascriptTemplates($javascriptTemplates) {
		foreach($javascriptTemplates as $template) {
			self::registerJavascriptTemplate($template);
		}
	}
	
	/**
	 * Imports a collection of JavaScript library locations.
	 *
	 * @param array $javascriptLibraries a collection of JavaScript library locations
	 */
	public static function importJavascriptLibraries($javascriptLibraries) {
		foreach($javascriptLibraries as $library) {
			self::registerJavascriptLibrary($library);
		}
	}
	
	/**
	 * Exports all currently registered JavaScript template locations.
	 *
	 * @return array all registered template locations
	 */
	public static function exportJavascriptTemplates() {
		return self::$_javascriptTemplates;
	}
	
	/**
	 * Exports all currently registered JavaScript library locations.
	 *
	 * @return array all registered library locations
	 */
	public static function exportJavascriptLibraries() {
		return self::$_javascriptLibraries;
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
			\Bedrock\Common\Logger::info('AJAX Request Detected');
			$result = true;
		}
		
		return $result;
	}
	
	abstract function render();
}