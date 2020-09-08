<?php
namespace Bedrock;

/**
 * Test: Bedrock\Control
 * 
 * @author Nick Williams
 * @version 1.0.2
 * @created 08/27/2012
 * @updated 09/07/2020
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
	 *
	 * @return void
	 */
	public function testSanitize() {
		// Setup
		$rawValue = 'it\'s it';
		$sanitizedValue = \Bedrock\Model::sanitize($rawValue);

		// Assertions
		$this->assertEquals('it\\\'s it', $sanitizedValue);
	}

	/**
	 * @covers Bedrock\Model::desanitize
	 *
	 * @return void
	 */
	public function testDesanitize() {
		// Setup
		$sanitizedValue = 'it\\\'s it';
		$rawValue = \Bedrock\Model::desanitize($sanitizedValue);

		// Assertions
		$this->assertEquals('it\'s it', $rawValue);
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
