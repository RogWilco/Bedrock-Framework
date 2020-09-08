<?php
/**
 * Test: Bedrock
 * 
 * @author Nick Williams
 * @version 1.0.3
 * @created 08/24/2012
 * @updated 09/07/2020
 */
class BedrockTest extends \Bedrock\Common\TestCase {
	/**
	 * @var Bedrock
	 */
	protected $_object;

	/**
	 * @var Bedrock\Common\Config
	 */
	protected $_mockConfig;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return void
	 */
	protected function setUp() {
		$this->_populateRegistry();
		$this->_mockConfig = $this->getMock('Bedrock\\Common\\Config', array('count'), array(
			array(
				'one' => 'unus',
				'two' => 'duo',
				'three' => 'tres',
				'four' => 'quattor'
			),
			false
		));

		$this->_object = new Bedrock($this->_mockConfig);
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
	 * @covers Bedrock::defaults
	 * @covers Bedrock::properties
	 *
	 * @return void
	 */
	public function testDefaults() {
		// Setup
		$this->_object->defaults();
		$properties = $this->_object->properties();

		// Assertions
		$this->assertInstanceOf('Bedrock\\Common\\Config', $properties);
		$this->assertCount(0, $properties);
	}

	/**
	 * @covers Bedrock::properties
	 *
	 * @return void
	 */
	public function testProperties() {
		// Setup
		$properties = $this->_object->properties();

		// Assertions
		$this->assertInstanceOf('Bedrock\\Common\\Config', $properties);
		$this->assertCount(4, $properties);

		foreach($this->_mockConfig as $key => $value) {
			$this->assertEquals($value, $properties[$key]);
		}
	}
}
