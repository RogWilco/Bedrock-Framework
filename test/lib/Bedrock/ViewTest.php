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
class ViewTest extends \Bedrock\Common\TestCase {
	/**
	 * @var View
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

		$this->_object = $this->getMockForAbstractClass('Bedrock\\View');
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
	 * @covers Bedrock\View::setValue
	 * @covers Bedrock\View::getValue
	 *
	 * @return void
	 */
	public function testSetValueAndGetValue() {
		// Setup
		$mockThree = new \stdClass();

		$this->_object->setValue('one', 'unus');
		$this->_object->setValue('two', null, 'duo');
		$this->_object->setValue('three', $mockThree);

		// Assertions
		$this->assertAttributeCount((3 + 1), '_values', $this->_object); //PHPUnit adds __phpunit_mockObjectId, so count ends up being +1

		$this->assertAttributeContains('unus', '_values', $this->_object);
		$this->assertEquals('unus', $this->_object->getValue('one'));

		$this->assertAttributeContains('duo', '_values', $this->_object);
		$this->assertEquals('duo', $this->_object->getValue('two'));

		$this->assertAttributeContains($mockThree, '_values', $this->_object);
		$this->assertSame($mockThree, $this->_object->getValue('three'));
	}

	/**
	 * @covers Bedrock\View::__set
	 * @covers Bedrock\View::setValue
	 * @covers Bedrock\View::__get
	 * @covers Bedrock\View::getValue
	 *
	 * @return void
	 */
	public function test__setAnd__get() {
		// Setup
		$mockThree = new \stdClass();

		$this->_object->__set('one', 'unus');
		$this->_object->two = 'duo';
		$this->_object->three = $mockThree;

		// Assertions
		$this->assertAttributeCount((3 + 1), '_values', $this->_object); //PHPUnit adds __phpunit_mockObjectId, so count ends up being +1

		$this->assertAttributeContains('unus', '_values', $this->_object);
		$this->assertEquals('unus', $this->_object->__get('one'));
		$this->assertEquals('unus', $this->_object->one);
		$this->assertEquals('unus', $this->_object->getValue('one'));

		$this->assertAttributeContains('duo', '_values', $this->_object);
		$this->assertEquals('duo', $this->_object->__get('two'));
		$this->assertEquals('duo', $this->_object->two);
		$this->assertEquals('duo', $this->_object->getValue('two'));

		$this->assertAttributeContains($mockThree, '_values', $this->_object);
		$this->assertSame($mockThree, $this->_object->__get('three'));
		$this->assertSame($mockThree, $this->_object->three);
		$this->assertSame($mockThree, $this->_object->getValue('three'));
	}

	/**
	 * @covers Bedrock\View::printValue
	 * @covers Bedrock\View::setValue
	 *
	 * @return void
	 */
	public function testPrintValue() {
		// Setup
		$this->_object->setValue('test', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit.');

		// Assertions
		$this->expectOutputString('Lorem ipsum dolor sit amet, consectetur adipisicing elit.');
		$this->_object->printValue('test');
	}

	/**
	 * @covers Bedrock\View::setForm
	 * @todo   Implement testSetForm().
	 *
	 * @return void
	 */
	public function testSetForm() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::getForm
	 * @todo   Implement testGetForm().
	 *
	 * @return void
	 */
	public function testGetForm() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::setMessage
	 * @todo   Implement testSetMessage().
	 *
	 * @return void
	 */
	public function testSetMessage() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::setErrors
	 * @todo   Implement testSetErrors().
	 *
	 * @return void
	 */
	public function testSetErrors() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::getMessages
	 * @todo   Implement testGetMessages().
	 *
	 * @return void
	 */
	public function testGetMessages() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::hasMessages
	 * @todo   Implement testHasMessages().
	 *
	 * @return void
	 */
	public function testHasMessages() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::printRoot
	 * @todo   Implement testPrintRoot().
	 *
	 * @return void
	 */
	public function testPrintRoot() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::registerCssStylesheet
	 * @todo   Implement testRegisterCssStylesheet().
	 *
	 * @return void
	 */
	public function testRegisterCssStylesheet() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::registerJavascriptTemplate
	 * @todo   Implement testRegisterJavascriptTemplate().
	 *
	 * @return void
	 */
	public function testRegisterJavascriptTemplate() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::registerJavascriptLibrary
	 * @todo   Implement testRegisterJavascriptLibrary().
	 *
	 * @return void
	 */
	public function testRegisterJavascriptLibrary() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::importJavascriptTemplates
	 * @todo   Implement testImportJavascriptTemplates().
	 *
	 * @return void
	 */
	public function testImportJavascriptTemplates() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::importJavascriptLibraries
	 * @todo   Implement testImportJavascriptLibraries().
	 *
	 * @return void
	 */
	public function testImportJavascriptLibraries() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::exportJavascriptTemplates
	 * @todo   Implement testExportJavascriptTemplates().
	 *
	 * @return void
	 */
	public function testExportJavascriptTemplates() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::exportJavascriptLibraries
	 * @todo   Implement testExportJavascriptLibraries().
	 *
	 * @return void
	 */
	public function testExportJavascriptLibraries() {
		// Remove the following lines when you implement this test.
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	/**
	 * @covers Bedrock\View::isAjaxRequest
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
