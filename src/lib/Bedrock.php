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
	public function __construct(Bedrock_Common_Config $options = null) {
		$this->defaults();
		$this->_config = Bedrock_Common_Registry::get('config');
		
		if($options instanceof Bedrock_Common_Config) {
			$this->_properties->merge($options);
		}
	}
	
	/**
	 * Applies all default properties for the current object. 
	 */
	public function defaults() {
		$this->_properties = new Bedrock_Common_Config(array(), true);
	}
	
	/**
	 * Retrieves the publicly available properties for the object.
	 *
	 * @return Bedrock_Common_Config the currently stored public properties
	 */
	public function properties() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			Bedrock_Common_Logger::logExit();
			return $this->_properties;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
}
?>