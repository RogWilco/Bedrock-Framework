<?php
/**
 * Base class for the Bedrock framework.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.1
 * @created 07/09/2008
 * @updated 08/27/2012
 */
class Bedrock {
	protected $_properties;
	protected $_propertiesKey;
	protected $_config;
	
	/**
	 * Initializes the object.
	 */
	public function __construct(\Bedrock\Common\Config $options = null) {
		$this->defaults();
		$this->_config = \Bedrock\Common\Registry::get('config');
		
		if($options instanceof \Bedrock\Common\Config) {
			$this->_properties->unlock($this->_propertiesKey);
			$this->_properties->merge($options);
			$this->_propertiesKey = $this->_properties->lock();
		}
	}
	
	/**
	 * Applies all default properties for the current object. 
	 */
	public function defaults() {
		$this->_properties = new \Bedrock\Common\Config(array(), true, $this->_propertiesKey);
	}
	
	/**
	 * Retrieves the publicly available properties for the object.
	 *
	 * @return \Bedrock\Common\Config the currently stored public properties
	 */
	public function properties() {
		return $this->_properties;
	}
}