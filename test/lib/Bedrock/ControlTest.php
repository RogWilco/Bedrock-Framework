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
class ControlTest extends \Bedrock\Common\TestCase {
	/**
	 * @var Control
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

		//$this->_object = $this->getMockForAbstractClass('Bedrock\\Control');
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
	 * @covers Bedrock\Control::redirect
	 * @todo   Implement testRedirect().
	 *
	 * @return void
	 */
	public function testRedirect() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Control::isAjaxRequest
	 * @todo   Implement testIsAjaxRequest().
	 *
	 * @return void
	 */
	public function testIsAjaxRequest() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}