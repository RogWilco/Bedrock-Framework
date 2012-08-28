<?php
namespace Bedrock;

/**
 * Test: Bedrock\Control
 *
 * @author Nick Williams
 * @version 1.0.0
 * @created
 * @updated
 */
class PluginTest extends \Bedrock\Common\TestCase {
	/**
	 * @var Plugin
	 */
	protected $_object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp() {
		$this->_populateRegistry();

		//$this->_object = $this->getMockForAbstractClass('Bedrock\\Plugin');
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown() {
		$this->_clearRegistry();
	}

	/**
	 * @covers Bedrock\Plugin::checkDependencies
	 * @todo   Implement testCheckDependencies().
	 *
	 * @return void
	 */
	public function testCheckDependencies() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Plugin::missingDependencies
	 * @todo   Implement testMissingDependencies().
	 *
	 * @return void
	 */
	public function testMissingDependencies() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Plugin::loadDependencies
	 * @todo   Implement testLoadDependencies().
	 *
	 * @return void
	 */
	public function testLoadDependencies() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
