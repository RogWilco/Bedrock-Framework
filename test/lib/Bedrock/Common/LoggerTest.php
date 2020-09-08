<?php
namespace Bedrock\Common;

/**
 * Test: Bedrock\Common\Logger
 * 
 * @author Nick Williams
 * @version 1.0.0
 * @created 09/07/2020
 * @updated 09/07/2020
 */
class LoggerTest extends \Bedrock\Common\TestCase {
	/**
	 * @var Logger an instance of the system under test
	 */
	protected $_object;

	/**
	 * @var string the class name for the system under test
	 */
	protected $_class = 'Bedrock\\Common\\Logger';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {
		//$this->_object = new Logger;
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {
	}

	/**
	 * @covers Bedrock\Common\Logger::init
	 * @todo   Implement testInit().
	 */
	public function testInit() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common\Logger::toArray
	 * @todo   Implement testToArray().
	 */
	public function testToArray() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common\Logger::addTarget
	 * @todo   Implement testAddTarget().
	 */
	public function testAddTarget() {
		// Setup
		$class = $this->_class;
		$mockTarget = $this->getMockForAbstractClass('Bedrock\\Common\\Logger\\Target\\TargetInterface', array('getFormat'));
		$class::addTarget($mockTarget, $class::LEVEL_TRAVERSE);

		// Mock Target
//		$mockTarget->expects($this->any())->method('getFormat')->withAnyParameters()->will($this->returnValue($class::OUTPUT_ARRAY));
//		$mockTarget->expects($this->any())->method('write')->with(array(
//			'msg' => array('TITLE', 'DATA'),
//			'time' => $class::getTime(),
//			'level' => $class::LEVEL_INFO,
//			'type' => 'array',
//			'class' => 'LoggerTest',
//			'function' => $caller['function']
//		));

		$this->markTestIncomplete();
	}

	/**
	 * @covers Bedrock\Common\Logger::levelToString
	 */
	public function testLevelToString() {
		// Setup
		$class = $this->_class;
		$levelTraverse = $class::levelToString($class::LEVEL_TRAVERSE);
		$levelInfo = $class::levelToString($class::LEVEL_INFO);
		$levelWarn = $class::levelToString($class::LEVEL_WARN);
		$levelError = $class::levelToString($class::LEVEL_ERROR);

		// Assertions
		$this->assertEquals('TRAVERSE', $levelTraverse);
		$this->assertEquals('INFO', $levelInfo);
		$this->assertEquals('WARN', $levelWarn);
		$this->assertEquals('ERROR', $levelError);
	}

	/**
	 * @covers Bedrock\Common\Logger::stringToLevel
	 */
	public function testStringToLevel() {
		// Setup
		$class = $this->_class;
		$levelTraverse = $class::stringToLevel('TRAVERSE');
		$levelInfo = $class::stringToLevel('INFO');
		$levelWarn = $class::stringToLevel('WARN');
		$levelError = $class::stringToLevel('ERROR');

		// Assertions
		$this->assertEquals($class::LEVEL_TRAVERSE, $levelTraverse);
		$this->assertEquals($class::LEVEL_INFO, $levelInfo);
		$this->assertEquals($class::LEVEL_WARN, $levelWarn);
		$this->assertEquals($class::LEVEL_ERROR, $levelError);
	}

	/**
	 * @covers Bedrock\Common\Logger::log
	 * @todo   Implement testLog().
	 */
	public function testLog() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common\Logger::info
	 * @todo   Implement testInfo().
	 */
	public function testInfo() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common\Logger::warn
	 * @todo   Implement testWarn().
	 */
	public function testWarn() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common\Logger::error
	 * @todo   Implement testError().
	 */
	public function testError() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common\Logger::exception
	 * @todo   Implement testException().
	 */
	public function testException() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common\Logger::table
	 * @todo   Implement testTable().
	 */
	public function testTable() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common\Logger::logEntry
	 * @todo   Implement testLogEntry().
	 */
	public function testLogEntry() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\Common\Logger::logExit
	 * @todo   Implement testLogExit().
	 */
	public function testLogExit() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
