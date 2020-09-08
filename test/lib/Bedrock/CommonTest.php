<?php
namespace Bedrock;

/**
 * Test: Bedrock\Common
 * 
 * @author Nick Williams
 * @version 1.0.3
 * @created 08/24/2012
 * @updated 09/07/2020
 */
class CommonTest extends \Bedrock\Common\TestCase {
	/**
	 * @var Common
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

		$this->_object = new Common;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return void
	 */
	protected function tearDown() {
		\Bedrock\Common\Registry::clear();
	}

	/**
	 * @covers Bedrock\Common::init
	 * @todo   Implement testInit().
	 *
	 * @return void
	 */
	public function testInit() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common::autoload
	 * @todo   Implement testAutoload().
	 *
	 * @return void
	 */
	public function testAutoload() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common::error
	 * @todo   Implement testError().
	 *
	 * @return void
	 */
	public function testError() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
