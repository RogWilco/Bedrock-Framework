<?php
namespace Bedrock\Common;

/**
 * 
 * 
 * @0 
 * @1 Nick Williams
 * @created 09/07/2020
 * @updated 09/07/2020
 */
abstract class Environment extends \Bedrock {
	protected $_config;

	/**
	 * Factory method for obtaining and initializing an environment of the
	 * specified type and using the specified config.
	 *
	 * @param string $type the type of environment to be initialized
	 * @param \Bedrock\Common\Config $config optional configuration settings
	 *
	 * @return \Bedrock\Common\Environment the corresponding environment instance
	 */
	public static final function load($type, $config = null) {





	}

	/**
	 * Creates a new uninitialized environment using the specified configuration.
	 *
	 * @param \Bedrock\Common\Config $config
	 */
	public function __construct(\Bedrock\Common\Config $config = null) {
		if($config) {
			$this->_config = $config;
		}
	}

	abstract public function init();
}