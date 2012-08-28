<?php
namespace Bedrock\Common;

/**
 * Provides additional functionality on top of the PHPUnit TestCase class.
 * 
 * @author Nick Williams
 * @version 1.0.1
 * @created 08/24/2012
 * @updated 08/27/2012
 */
class TestCase extends \PHPUnit_Framework_TestCase {
	/**
	 * Populates the registry with some default values/objects suitable for running tests.
	 *
	 * @return void
	 */
	protected function _populateRegistry() {
		// Clear any existing data.
		\Bedrock\Common\Registry::clear();

		// Mock: Config
		$mockConfig = new \Bedrock\Common\Config(array(
			'root' => array(
				'web' => ''
			),
			'template' => 'default'
		), false);

		\Bedrock\Common\Registry::set('config', $mockConfig);

		// Mock: Database
		\Bedrock\Common\Registry::set('database', new \stdClass());
	}

	/**
	 * Clears the registry of all values/objects.
	 *
	 * @return void
	 */
	protected function _clearRegistry() {
		\Bedrock\Common\Registry::clear();
	}

	/**
	 * @param string $id the unique identifier to associate with the value
	 * @param mixed $value the value to be stored
	 *
	 * @return void
	 */
	protected function _addToRegistry($id, $value) {
		\Bedrock\Common\Registry::set($id, $value);
	}

	/**
	 * Builds a mock Bedrock\Common\Config object based on the specified array. Useful for building nested config
	 * objects.
	 *
	 * @param array $array the data with which to build the object
	 *
	 * @return \PHPUnit_Framework_MockObject_MockObject
	 */
	protected function _buildConfig($array = array()) {
		// Setup
		$data = array();

		foreach($array as $key => $value) {
			if(is_array($value)) {
				$data[$key] = $this->_buildConfig($value);
			}
			else {
				$data[$key] = $value;
			}
		}

		return $this->getMock('Bedrock\\Common\\Config', array('__get', '__set'), array($data, false));
	}
}