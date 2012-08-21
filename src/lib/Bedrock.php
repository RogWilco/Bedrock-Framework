<?php
/**
 * Base class for the Bedrock framework.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 07/09/2008
 * @updated 07/09/2008
 */
class Bedrock {
	protected $_properties;
	protected $_config;
	
	/**
	 * Initializes the object.
	 */
	public function __construct(\Bedrock\Common\Config $options = null) {
		$this->defaults();
		$this->_config = \Bedrock\Common\Registry::get('config');
		
		if($options instanceof \Bedrock\Common\Config) {
			$this->_properties->merge($options);
		}
	}
	
	/**
	 * Applies all default properties for the current object. 
	 */
	public function defaults() {
		$this->_properties = new \Bedrock\Common\Config(array(), true);
	}
	
	/**
	 * Retrieves the publicly available properties for the object.
	 *
	 * @return \Bedrock\Common\Config the currently stored public properties
	 */
	public function properties() {
		\Bedrock\Common\Logger::logEntry();
		
		try {
			\Bedrock\Common\Logger::logExit();
			return $this->_properties;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
		}
	}
}