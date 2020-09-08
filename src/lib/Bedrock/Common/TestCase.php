<?php
namespace Bedrock\Common;

/**
 * Provides additional functionality on top of the PHPUnit TestCase class.
 * 
 * @author Nick Williams
 * @version 1.0.3
 * @created 08/24/2012
 * @updated 09/07/2020
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
	 * Obtain the value of a private or protected property on an object.
	 *
	 * @param object $object the object from which to retrieve the property
	 * @param string $property the name of the desired property
	 *
	 * @return mixed the corresponding value
	 */
	protected function _getPrivateProperty($object, $property) {
		$reflection = new \ReflectionProperty(get_class($object), $property);
		$reflection->setAccessible(true);

		return $reflection->getValue($object);
	}
}