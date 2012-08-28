<?php
namespace Bedrock\Common;

/**
 * Provides additional functionality on top of the PHPUnit TestCase class.
 *
 * @author Nick Williams
 * @version 1.0.0
 * @created
 * @updated
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
		\Bedrock\Common\Registry::set('config', new \stdClass());
	}
}