<?php
/**
 * Tests main Bedrock class.
 * 
 * @author Nick Williams
 * @version 1.0.1
 * @created 8/24/2012
 * @updated 08/24/2012
 */
class BedrockTest extends \Bedrock\Common\TestCase {
	/**
	 * @var Bedrock
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

		$this->_object = new Bedrock;
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
	 * @covers Bedrock::defaults
	 * @todo   Implement testDefaults().
	 *
	 * @return void
	 */
	public function testDefaults() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock::properties
	 * @todo   Implement testProperties().
	 *
	 * @return void
	 */
	public function testProperties() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
