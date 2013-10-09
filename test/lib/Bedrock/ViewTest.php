<?php
namespace Bedrock;

/**
 * Test: Bedrock\View
 * 
 * @author Nick Williams
 * @version 1.0.2
 * @created 08/27/2012
 * @updated 08/28/2012
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
	 * @covers Bedrock\View::getForm
	 *
	 * @return void
	 */
	public function testSetFormAndGetForm() {
		// Setup
		$mockForm = $this->getMock('Bedrock\\Common\\Form\\Generator');
		$this->_object->setForm('TestForm', $mockForm);

		// Assertions
		$this->assertAttributeContains($mockForm, '_forms', $this->_object);
		$this->assertSame($mockForm, $this->_object->getForm('TestForm'));
	}

	/**
	 * @covers Bedrock\View::setMessage
	 *
	 * @return void
	 */
	public function testSetMessage() {
		// Setup
		$messages = array(
			'success' => array('Testing message type: SUCCESS'),
			'info' => array('Testing message type: INFO'),
			'warn' => array('Testing message type: WARN'),
			'error' => array('Testing message type: ERROR')
		);

		$this->_object->setMessage(\Bedrock\View::MESSAGE_SUCCESS, $messages['success'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_INFO, $messages['info'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_WARN, $messages['warn'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_ERROR, $messages['error'][0]);

		// Assertions
		$this->assertAttributeSame($messages, '_messages', $this->_object);
	}

	/**
	 * @covers Bedrock\View::setErrors
	 *
	 * @return void
	 */
	public function testSetErrors() {
		// Setup
		$errors = array(
			array('msg' => 'one'),
			array('msg' => 'two'),
			array('msg' => 'three'),
			array('msg' => 'four'),
			array('msg' => 'five')
		);

		$this->_object->setErrors($errors);

		// Assertions
		$this->assertAttributeSame(
			array(
				'error' => array(
					'one',
					'two',
					'three',
					'four',
					'five'
				)
			),
			'_messages',
			$this->_object
		);
	}

	/**
	 * @covers Bedrock\View::getMessages
	 * @covers Bedrock\View::setMessage
	 *
	 * @return void
	 */
	public function testGetMessages() {
		// Setup
		$messages = array(
			'success' => array(
				'Testing message type: SUCCESS_01',
				'Testing message type: SUCCESS_02',
				'Testing message type: SUCCESS_03'
			),
			'info' => array(
				'Testing message type: INFO_01',
				'Testing message type: INFO_02',
				'Testing message type: INFO_03'
			),
			'warn' => array(
				'Testing message type: WARN_01',
				'Testing message type: WARN_02',
				'Testing message type: WARN_03'
			),
			'error' => array(
				'Testing message type: ERROR_01',
				'Testing message type: ERROR_02',
				'Testing message type: ERROR_03'
			)
		);

		$this->_object->setMessage(\Bedrock\View::MESSAGE_SUCCESS, $messages['success'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_SUCCESS, $messages['success'][1]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_SUCCESS, $messages['success'][2]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_INFO, $messages['info'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_INFO, $messages['info'][1]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_INFO, $messages['info'][2]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_WARN, $messages['warn'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_WARN, $messages['warn'][1]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_WARN, $messages['warn'][2]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_ERROR, $messages['error'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_ERROR, $messages['error'][1]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_ERROR, $messages['error'][2]);

		// Assertions
		$this->assertAttributeSame($messages, '_messages', $this->_object);
		$this->assertSame($messages, $this->_object->getMessages(\Bedrock\View::MESSAGE_ALL));
		$this->assertSame($messages['success'], $this->_object->getMessages(\Bedrock\View::MESSAGE_SUCCESS));
		$this->assertSame($messages['info'], $this->_object->getMessages(\Bedrock\View::MESSAGE_INFO));
		$this->assertSame($messages['warn'], $this->_object->getMessages(\Bedrock\View::MESSAGE_WARN));
		$this->assertSame($messages['error'], $this->_object->getMessages(\Bedrock\View::MESSAGE_ERROR));
	}

	/**
	 * Asserts that no messages are currently stored in the SUT.
	 *
	 * @param View $object the object to be inspected
	 *
	 * @return void
	 */
	protected function _assertNoMessages(\Bedrock\View $object) {
		// Assertions
		$this->assertAttributeSame(array(), '_messages', $object);
		$this->assertFalse($object->hasMessages());
		$this->assertFalse($object->hasMessages(\Bedrock\View::MESSAGE_ALL));
		$this->assertFalse($object->hasMessages(\Bedrock\View::MESSAGE_SUCCESS));
		$this->assertFalse($object->hasMessages(\Bedrock\View::MESSAGE_INFO));
		$this->assertFalse($object->hasMessages(\Bedrock\View::MESSAGE_WARN));
		$this->assertFalse($object->hasMessages(\Bedrock\View::MESSAGE_ERROR));
	}

	/**
	 * Asserts that only messages of the specified type are currently stored in the SUT.
	 *
	 * @param int $type the message type to be checked
	 * @param View $object the object to be inspected
	 *
	 * @return void
	 */
	protected function _assertOnlyMessagesOfType($type, \Bedrock\View $object) {
		// Setup
		$messageTypes = array(
			\Bedrock\View::MESSAGE_SUCCESS,
			\Bedrock\View::MESSAGE_INFO,
			\Bedrock\View::MESSAGE_WARN,
			\Bedrock\View::MESSAGE_ERROR
		);

		$this->assertTrue($object->hasMessages());
		$this->assertTrue($object->hasMessages(\Bedrock\View::MESSAGE_ALL));

		foreach($messageTypes as $messageType) {
			if($messageType === $type) {
				$this->assertTrue($this->_object->hasMessages($messageType));
			}
			else {
				$this->assertFalse($this->_object->hasMessages($messageType));
			}
		}
	}

	/**
	 * @covers Bedrock\View::hasMessages
	 * @covers Bedrock\View::setMessage
	 *
	 * @return void
	 */
	public function testHasMessages() {
		// Setup
		$messages = array(
			'success' => array('Testing message type: SUCCESS'),
			'info' => array('Testing message type: INFO'),
			'warn' => array('Testing message type: WARN'),
			'error' => array('Testing message type: ERROR')
		);

		// Assertions
		$this->_assertNoMessages($this->_object);

		$this->_object->setMessage(\Bedrock\View::MESSAGE_SUCCESS, $messages['success'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_INFO, $messages['info'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_WARN, $messages['warn'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_ERROR, $messages['error'][0]);

		$this->assertTrue($this->_object->hasMessages());
		$this->assertTrue($this->_object->hasMessages(\Bedrock\View::MESSAGE_ALL));
		$this->assertTrue($this->_object->hasMessages(\Bedrock\View::MESSAGE_SUCCESS));
		$this->assertTrue($this->_object->hasMessages(\Bedrock\View::MESSAGE_INFO));
		$this->assertTrue($this->_object->hasMessages(\Bedrock\View::MESSAGE_WARN));
		$this->assertTrue($this->_object->hasMessages(\Bedrock\View::MESSAGE_ERROR));
	}

	/**
	 * @covers Bedrock\View::hasMessages
	 * @covers Bedrock\View::setMessage
	 *
	 * @return void
	 */
	public function testHasMessagesOnlySuccess() {
		// Setup
		$messages = array(
			'success' => array(
				'Testing message type: SUCCESS_01',
				'Testing message type: SUCCESS_02',
				'Testing message type: SUCCESS_03'
			)
		);

		// Assertions
		$this->_assertNoMessages($this->_object);

		$this->_object->setMessage(\Bedrock\View::MESSAGE_SUCCESS, $messages['success'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_SUCCESS, $messages['success'][1]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_SUCCESS, $messages['success'][2]);

		$this->_assertOnlyMessagesOfType(\Bedrock\View::MESSAGE_SUCCESS, $this->_object);
		$this->assertAttributeSame($messages, '_messages', $this->_object);
	}

	/**
	 * @covers Bedrock\View::hasMessages
	 * @covers Bedrock\View::setMessage
	 *
	 * @return void
	 */
	public function testHasMessagesOnlyInfo() {
		// Setup
		$messages = array(
			'info' => array(
				'Testing message type: INFO_01',
				'Testing message type: INFO_02',
				'Testing message type: INFO_03'
			)
		);

		// Assertions
		$this->_assertNoMessages($this->_object);

		$this->_object->setMessage(\Bedrock\View::MESSAGE_INFO, $messages['info'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_INFO, $messages['info'][1]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_INFO, $messages['info'][2]);

		$this->_assertOnlyMessagesOfType(\Bedrock\View::MESSAGE_INFO, $this->_object);
		$this->assertAttributeSame($messages, '_messages', $this->_object);
	}

	/**
	 * @covers Bedrock\View::hasMessages
	 * @covers Bedrock\View::setMessage
	 *
	 * @return void
	 */
	public function testHasMessagesOnlyWarn() {
		// Setup
		$messages = array(
			'warn' => array(
				'Testing message type: WARN_01',
				'Testing message type: WARN_02',
				'Testing message type: WARN_03'
			)
		);

		// Assertions
		$this->_assertNoMessages($this->_object);

		$this->_object->setMessage(\Bedrock\View::MESSAGE_WARN, $messages['warn'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_WARN, $messages['warn'][1]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_WARN, $messages['warn'][2]);

		$this->_assertOnlyMessagesOfType(\Bedrock\View::MESSAGE_WARN, $this->_object);
		$this->assertAttributeSame($messages, '_messages', $this->_object);
	}

	/**
	 * @covers Bedrock\View::hasMessages
	 * @covers Bedrock\View::setMessage
	 *
	 * @return void
	 */
	public function testHasMessagesOnlyError() {
		// Setup
		$messages = array(
			'error' => array(
				'Testing message type: ERROR_01',
				'Testing message type: ERROR_02',
				'Testing message type: ERROR_03'
			)
		);

		// Assertions
		$this->_assertNoMessages($this->_object);

		$this->_object->setMessage(\Bedrock\View::MESSAGE_ERROR, $messages['error'][0]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_ERROR, $messages['error'][1]);
		$this->_object->setMessage(\Bedrock\View::MESSAGE_ERROR, $messages['error'][2]);

		$this->_assertOnlyMessagesOfType(\Bedrock\View::MESSAGE_ERROR, $this->_object);
		$this->assertAttributeSame($messages, '_messages', $this->_object);
	}

	/**
	 * @covers Bedrock\View::printRoot
	 *
	 * @return void
	 */
	public function testPrintRoot() {
		$this->expectOutputString($this->_getPrivateProperty($this->_object, '_root'));
		$this->_object->printRoot();
	}

	/**
	 * @covers Bedrock\View::printRoot
	 *
	 * @return void
	 */
	public function testPrintRootForRoot() {
		$this->expectOutputString($this->_getPrivateProperty($this->_object, '_root'));
		$this->_object->printRoot('root');
	}

	/**
	 * @covers Bedrock\View::printRoot
	 *
	 * @return void
	 */
	public function testPrintRootForWeb() {
		$this->expectOutputString($this->_getPrivateProperty($this->_object, '_webroot'));
		$this->_object->printRoot('web');
	}

	/**
	 * @covers Bedrock\View::printRoot
	 *
	 * @return void
	 */
	public function testPrintRootForImg() {
		$this->expectOutputString($this->_getPrivateProperty($this->_object, '_imgroot'));
		$this->_object->printRoot('img');
	}

	/**
	 * @covers Bedrock\View::printRoot
	 *
	 * @return void
	 */
	public function testPrintRootForPage() {
		$this->expectOutputString($this->_getPrivateProperty($this->_object, '_pageroot'));
		$this->_object->printRoot('page');
	}

	/**
	 * @covers Bedrock\View::registerCssStylesheet
	 *
	 * @return void
	 */
	public function testRegisterCssStylesheet() {
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);

		// Setup
		// $this->_object->registerCssStylesheet('test.css');
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
	 *
	 * @return void
	 */
	public function testIsAjaxRequest() {
		// Assertions
		$_POST['ajax'] = 1;
		$this->assertTrue($this->_object->isAjaxRequest());

		$_POST['ajax'] = 0;
		$this->assertFalse($this->_object->isAjaxRequest());
	}
}
