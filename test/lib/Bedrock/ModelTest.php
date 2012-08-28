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
class ModelTest extends \Bedrock\Common\TestCase {
	/**
	 * @var Model
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

		//$this->_object = $this->getMockForAbstractClass('Bedrock\\Model');
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
	 * @covers Bedrock\Model::sanitize
	 * @todo   Implement testSanitize().
	 *
	 * @return void
	 */
	public function testSanitize() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Model::desanitize
	 * @todo   Implement testDesanitize().
	 *
	 * @return void
	 */
	public function testDesanitize() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Model::writeFile
	 * @todo   Implement testWriteFile().
	 *
	 * @return void
	 */
	public function testWriteFile() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
