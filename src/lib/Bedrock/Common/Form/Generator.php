<?php
/**
 * Form Generator Class
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 05/25/2008
 * @updated 05/25/2008
 */
class Bedrock_Common_Form_Generator extends Bedrock {
	protected $_fields;
	protected $_name;
	protected $_method;
	protected $_action;
	protected $_newLine;
	protected $_tab;
	
	// Text Symbols
	const NEWLINE = "\n";
	const TAB = "\t";
	
	// Base Form Types
	const TYPE_TEXT = 0;
	const TYPE_PASSWORD = 1;
	const TYPE_CHECKBOX = 2;
	const TYPE_RADIO = 3;
	const TYPE_HIDDEN = 4;
	const TYPE_FILE = 5;
	const TYPE_TEXTAREA = 6;
	const TYPE_SELECT = 7;
	
	// Button Types
	const TYPE_BTN_SUBMIT = 8;
	const TYPE_BTN_RESET = 9;
	const TYPE_BTN_IMG = 10;
	const TYPE_BTN = 11;
	
	// Advanced Form Types
	const TYPE_ADV_PASSWORD = 12;
	const TYPE_ADV_DATETIME = 13;
	const TYPE_ADV_DATE = 14;
	const TYPE_ADV_DATE_Y = 15;
	const TYPE_ADV_DATE_M = 16;
	const TYPE_ADV_DATE_D = 17;
	const TYPE_ADV_TIME = 18;
	const TYPE_ADV_TIME_H = 19;
	const TYPE_ADV_TIME_M = 10;
	const TYPE_ADV_TIME_S = 21;
	const TYPE_ADV_STATE = 22;
	
	/**
	 * The default constructor, initializing the object and optionally setting
	 * the form's name, method, and action.
	 *
	 * @param string $name the name of the form
	 * @param string $method the method for the form
	 * @param string $action the action for the form
	 */
	public function __construct($name = 'form', $method = 'POST', $action = '') {
		// Setup
		$this->_newLine = "\n";
		$this->_tab = "\t";
		
		// Set Initial Values
		$this->setName($name);
		$this->setMethod($method);
		$this->setAction($action);
	}
	
	/**
	 * Sets the name of the form.
	 *
	 * @param string $name the name to use for the form
	 */
	public function setName($name = 'form') {
		$this->_name = $name;
	}
	
	/**
	 * Returns the name of the form.
	 *
	 * @return string the current name of the form
	 */
	public function getName() {
		return $this->_name;
	}
	
	/**
	 * Outputs the name of the form to the browser.
	 */
	public function printName() {
		echo $this->getName();
	}
	
	/**
	 * Sets the method of the form.
	 *
	 * @param string $method the form method to use
	 */
	public function setMethod($method = 'POST') {
		switch(strtoupper($method)) {
			default:
			case 'POST':
				$this->_method = 'POST';
				break;
			case 'GET':
				$this->_method = 'GET';
				break;
		}
	}
	
	/**
	 * Returns the method of the form.
	 *
	 * @return string the current method of the form
	 */
	public function getMethod() {
		return $this->_method;
	}
	
	/**
	 * Outputs the method of the form.
	 */
	public function printMethod() {
		echo $this->getMethod();
	}
	
	/**
	 * Sets the action of the form.
	 *
	 * @param string $action the URL to which the form will be submitted
	 */
	public function setAction($action = '') {
		$this->_action = $action;
	}
	
	/**
	 * Returns the action of the form.
	 *
	 * @return string the current action of the form
	 */
	public function getAction() {
		return $this->_action;
	}
	
	/**
	 * Outputs the action of the form.
	 */
	public function printAction() {
		echo $this->getAction();
	}
	
	/**
	 * Adds a field to the form.
	 *
	 * @param string $name a name for the field that is unique within the context of the form
	 * @param string $label a label (or "friendly name") that can be displayed next to the input field
	 * @param string $type the type of input field
	 * @param boolean $required whether or not the field is required (allows for special formatting)
	 * @param array $properties additional properties for the HTML element as an array of name/property pairs
	 */
	public function addField($name, $label, $type, $value = '', $required = false, $options = array(), $rows = 8, $cols = 80, $properties = array()) {
		$this->_fields[$name]['label'] = $label;
		$this->_fields[$name]['type'] = $type;
		$this->_fields[$name]['value'] = $value;
		$this->_fields[$name]['required'] = $required;
		$this->_fields[$name]['options'] = $options;
		$this->_fields[$name]['rows'] = $rows;
		$this->_fields[$name]['cols'] = $cols;
		$this->_fields[$name]['properties'] = $properties;
	}
	
	/**
	 * Returns the field HTML as a string.
	 *
	 * @param string $name the name of the field to get
	 * @return string the field HTML
	 */
	public function getField($name) {
		// Setup
		$result = '';
		
		// Attempt to Retrieve Field
		if(isset($this->_fields[$name])) {
			$field = $this->_fields[$name];
			
			switch($field['type']) {
				case self::TYPE_BTN:
					$result = self::getButton($name, $field['value'], $field['properties']);
					break;
					
				case self::TYPE_BTN_SUBMIT:
					$result = self::getButtonSubmit($name, $field['value'], $field['properties']);
					break;
					
				case self::TYPE_BTN_RESET:
					$result = self::getButtonReset($name, $field['value'], $field['properties']);
					break;
				
				case self::TYPE_TEXT:
					$result = self::getInputText($name, $field['value'], $field['properties']);
					break;
					
				case self::TYPE_PASSWORD:
					$result = self::getInputPassword($name, $field['value'], $field['properties']);
					break;
					
				case self::TYPE_CHECKBOX:
					$result = self::getInputCheckbox($name, $field['options'][0], $field['value'], $field['properties']);
					break;
					
				case self::TYPE_RADIO:
					$result = self::getInputRadio($name, $field['options'], $field['value'], $field['properties']);
					break;
					
				case self::TYPE_HIDDEN:
					$result = self::getInputHidden($name, $field['value'], $field['properties']);
					break;
					
				case self::TYPE_FILE:
					$result = self::getInputFile($name, $field['properties']);
					break;
					
				case self::TYPE_TEXTAREA:
					$result = self::getInputTextarea($name, $field['value'], $field['rows'], $field['cols'], $field['properties']);
					break;
					
				case self::TYPE_SELECT:
					$result = self::getInputSelect($name, $field['options'], $field['value'], $field['properties']);
					break;
					
				case self::TYPE_ADV_DATE:
					$result = self::getInputAdvDate($name, $field['value'], $field['properties']);
					break;
					
				case self::TYPE_ADV_DATE_Y:
					$result = self::getInputAdvDateY($name, $field['value'], $field['properties']);
					break;
					
				case self::TYPE_ADV_DATE_M:
					$result = self::getInputAdvDateM($name, $field['value'], 'text', $field['properties']);
					break;
					
				case self::TYPE_ADV_DATE_D:
					$result = self::getInputAdvDateD($name, $field['value'], $field['properties']);
					break;
					
				case self::TYPE_ADV_STATE:
					$result = self::getInputAdvState($name, $field['value'], $field['properties']);
					break;
			}
		}
		
		return $result;
	}
	
	/**
	 * Returns the label for the specified field name.
	 *
	 * @param string $name the name of the field to retrieve the label for
	 * @return string the specified field's label
	 */
	public function getLabel($name) {
		// Setup
		$result = '';
		
		if(isset($this->_fields[$name])) {
			$result = $this->_fields[$name]['label'];
		}
		
		return $result;
	}
	
	/**
	 * Returns the value for the specified field.
	 *
	 * @param string $name the name of the field to retrieve the value for
	 * @return string the specified field's value
	 */
	public function getValue($name) {
		// Setup
		$result = '';
		
		if(isset($this->_fields[$name])) {
			$result = $this->_fields[$name]['value'];
		}
		
		return $result;
	}
	
	/**
	 * Outputs the value of the specified field to the browser.
	 *
	 * @param string $name the name of the field from which to output the value
	 */
	public function printValue($name) {
		echo $this->_fields[$name]['value'];
	}
	
	/**
	 * Returns whether or not the specified field is required.
	 *
	 * @param string $name the name of the field to check
	 * @return boolean whether or not the field is required
	 */
	public function isRequired($name) {
		// Setup
		$result = false;
		
		if(isset($this->_fields[$name])) {
			$result = $this->_fields[$name]['required'];
		}
		
		return $result;
	}
	
	/**
	 * Outputs the specified field's HTML to the browser.
	 *
	 * @param string $name the name of the field to output
	 */
	public function printField($name) {
		echo $this->getField($name);
	}
	
	/**
	 * Outputs the specified field's label to the browser.
	 *
	 * @param string $name the name of the field to use
	 */
	public function printLabel($name) {
		echo $this->getLabel($name);
	}
	
	/**
	 * Builds a string of property values from the supplied array to use within
	 * an HTML element tag.
	 *
	 * @param array $properties an array of name/value pairs
	 * @return string a string of properties that can be inserted into an HTML tag
	 */
	protected static function getPropString($properties = array()) {
		// Setup
		$propString = '';
		
		if(count($properties) > 0) {
			foreach($properties as $property) {
				$propString .= $property['name'] . '="' . $property['value'] . '" ';
			}
		}
		
		return $propString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a submit button.
	 *
	 * @param string $name the name of the button
	 * @param string $value the text value for the button to display
	 * @param array $properties an array of element property/value pairs to add to the button tag
	 * @return string the assembled HTML
	 */
	public static function getButtonSubmit($name = 'submit', $value = 'Submit', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		
		// Build HTML
		$buttonString = '<input type="submit" name="' . $name . '" id="' . $name . '" value="' . $value . '" ';
		$buttonString .= self::getPropString($properties) . '/>' . $n;
		
		return $buttonString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a reset button.
	 *
	 * @param string $name the name of the button
	 * @param string $value the text value for the button to display
	 * @param array $properties an array of element property/value pairs to add to the button tag
	 * @return string the assembled HTML
	 */
	public static function getButtonReset($name = 'reset', $value = 'Reset', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		
		// Build HTML
		$buttonString = '<input type="reset" name="' . $name . '" id="' . $name . '" value="' . $value . '" ';
		$buttonString .= self::getPropString($properties) . '/>' . $n;
		
		return $buttonString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a standard button.
	 *
	 * @param string $name the name of the button
	 * @param string $value the text value for the button to display
	 * @param array $properties an array of element property/value pairs to add to the button tag
	 * @return string the assembled HTML
	 */
	public static function getButton($name = 'button', $value = 'Button', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		
		// Build HTML
		$buttonString = '<input type="button" name="' . $name . '" id="' . $name . '" value="' . $value . '" ';
		$buttonString .= self::getPropString($properties) . '/>' . $n;
		
		return $buttonString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a standard text input field.
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputText($name = 'input_text', $value = '', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		
		// Build HTML
		$fieldString = '<input type="text" name="' . $name . '" id="' . $name . '" value="' . $value . '" ';
		$fieldString .= self::getPropString($properties) . '/>' . $n;
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a standard password input field.
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputPassword($name = 'input_password', $value = '', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		
		// Build HTML
		$fieldString = '<input type="password" name="' . $name . '" id="' . $name . '" value="' . $value . '" ';
		$fieldString .= self::getPropString($properties) . '/>' . $n;
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a standard checkbox input field.
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputCheckbox($name = 'input_checkbox', $option, $value = '', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		
		// Build HTML
		$fieldString = '<input type="checkbox" name="' . $name . '" id="' . $name . '" value="' . $option . '" ';
		
		if($value == $option) {
			$fieldString .= 'checked="checked" ';
		}
		
		$fieldString .= self::getPropString($properties) . '/>' . $n;
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a standard radio button field collection.
	 *
	 * @param string $name the name of the field
	 * @param array $options an array of label/value pairs for the options
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputRadio($name = 'input_radio', $options = array(), $value = '', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		
		// Build HTML
		$fieldString = '';
		$propString = self::getPropString($properties);
		
		foreach($options as $key => $option) {
			$fieldString .= '<input type="radio" name="' . $name . '" id="' . $name . '_' . $key . '" value="' . $option['value'] . '" ';
			
			if($value == $option['value']) {
				$fieldString .= 'checked="checked" ';
			}
			
			$fieldString .= $propString . '/>&nbsp;' . $option['label'] . $n;
		}
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a standard hidden input field.
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputHidden($name = 'input_hidden', $value = '', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		
		// Build HTML
		$fieldString = '<input type="hidden" name="' . $name . '" id="' . $name . '" value="' . $value . '" ';
		$fieldString .= self::getPropString($properties) . '/>' . $n;
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a file input field.
	 *
	 * @param string $name the name of the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputFile($name = 'input_file', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		
		// Build HTML
		$fieldString = '<input type="file" name="' . $name . '" id="' . $name . '" ' . self::getPropString($properties) . '/>' . $n;
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a textarea input field.
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param integer $rows the number of rows the input should include
	 * @param integer $cols the number of columns (or characters-across) the field should have
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputTextarea($name = 'input_textarea', $value = '', $rows = 8, $cols = 80, $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		$t = self::TAB;
		
		// Build HTML
		$fieldString = '<textarea name="' . $name . '" id="' . $name . '" rows="' . $rows . '" cols="' . $cols . '" ' . self::getPropString($properties) . '>' . $n;
		$fieldString .= $t . $value . $n;
		$fieldString .= '</textarea>';
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a selection input field.
	 *
	 * @param string $name the name of the field
	 * @param array $options an array of label/value pairs to use for the options
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputSelect($name = 'input_select', $options = array(), $value = '', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		$t = self::TAB;
		
		// Build HTML
		$fieldString = '<select name="' . $name . '" id="' . $name . '" ' . self::getPropString($properties) . ' >' . $n;
		
		foreach($options as $option) {
			$fieldString .= $t . '<option value="' . $option['value'] . '"';
			
			if($value == $option['value']) {
				$fieldString .= ' selected="selected"';
			}
			
			$fieldString .= '>' . $option['label'] . '</option>' . $n;
		}
		
		$fieldString .= '</select>' . $n;
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * an advanced password input field, wtih two inputs that must match (if
	 * validation is used).
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputAdvPassword($name = 'input_adv_password', $value = '', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		
		// Build HTML
		$fieldString = '<input type="password" name="' . $name . '_a" id="' . $name . '_a" value="' . $value . '" ';
		$fieldString .= self::getPropString($properties) . '/>' . $n;
		
		$fieldString .= '<br />';
		
		$fieldString .= '<input type="password" name="' . $name . '_b" id="' . $name . '_b" value="' . $value . '" ';
		$fieldString .= self::getPropString($properties) . '/>' . $n;
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * date dropdown fields.
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputAdvDate($name = 'input_adv_date', $value = '', $properties = array()) {
		// Setup
		$y = '';
		$m = '';
		$d = '';
		
		if($value != '') {
			$y = substr($value, 0, 4);
			$m = substr($value, 5, 2);
			$d = substr($value, 8, 2);
		}
		
		// Build HTML
		$fieldString = self::getInputAdvDateM($name . '_m', $m, 'text', $properties) . '&nbsp;';
		$fieldString .= self::getInputAdvDateD($name . '_d', $d, $properties) . ',&nbsp;';
		$fieldString .= self::getInputAdvDateY($name . '_y', $y, $properties);
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a year dropdown field.
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputAdvDateY($name = 'input_adv_date_y', $value = '', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		$t = self::TAB;
		
		// Build HTML
		$fieldString = '<select name="' . $name . '" id="' . $name . '" ' . self::getPropString($properties) . '>' . $n;
		$fieldString .= $t . '<option value="">----</option>' . $n;
		
		for($i = date('Y'); $i > date('Y') - 100; $i--) {
			$fieldString .= $t . '<option value="' . $i . '"';
			
			if($value == $i) {
				$fieldString .= ' selected="selected"';
			}
			
			$fieldString .= '>' . $i . '</option>' . $n;
		}
		
		$fieldString .= '</select>' . $n;
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a month dropdown field.
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param string $type the format type for the month (either text or numeric)
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputAdvDateM($name = 'input_adv_date_m', $value = '', $type = 'text', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		$t = self::TAB;
		
		// Build HTML
		$fieldString = '<select name="' . $name . '" id="' . $name . '" ' . self::getPropString($properties) . '>' . $n;
		
		if($type == 'text') {
			$fieldString .= $t . '<option value="">--</option>' . $n;
			$fieldString .= $t . '<option value="01"' . ($value == 1 ? 'selected="selected"' : '') . '>January</option>' . $n;
			$fieldString .= $t . '<option value="02"' . ($value == 2 ? 'selected="selected"' : '') . '>February</option>' . $n;
			$fieldString .= $t . '<option value="03"' . ($value == 3 ? 'selected="selected"' : '') . '>March</option>' . $n;
			$fieldString .= $t . '<option value="04"' . ($value == 4 ? 'selected="selected"' : '') . '>April</option>' . $n;
			$fieldString .= $t . '<option value="05"' . ($value == 5 ? 'selected="selected"' : '') . '>May</option>' . $n;
			$fieldString .= $t . '<option value="06"' . ($value == 6 ? 'selected="selected"' : '') . '>June</option>' . $n;
			$fieldString .= $t . '<option value="07"' . ($value == 7 ? 'selected="selected"' : '') . '>July</option>' . $n;
			$fieldString .= $t . '<option value="08"' . ($value == 8 ? 'selected="selected"' : '') . '>August</option>' . $n;
			$fieldString .= $t . '<option value="09"' . ($value == 9 ? 'selected="selected"' : '') . '>September</option>' . $n;
			$fieldString .= $t . '<option value="10"' . ($value == 10 ? 'selected="selected"' : '') . '>October</option>' . $n;
			$fieldString .= $t . '<option value="11"' . ($value == 11 ? 'selected="selected"' : '') . '>November</option>' . $n;
			$fieldString .= $t . '<option value="12"' . ($value == 12 ? 'selected="selected"' : '') . '>December</option>' . $n;
		}
		elseif($type == 'numeric') {
			$fieldString .= $t . '<option value="">--</option>' . $n;
			$fieldString .= $t . '<option value="01"' . ($value == 1 ? 'selected="selected"' : '') . '>01</option>' . $n;
			$fieldString .= $t . '<option value="02"' . ($value == 2 ? 'selected="selected"' : '') . '>02</option>' . $n;
			$fieldString .= $t . '<option value="03"' . ($value == 3 ? 'selected="selected"' : '') . '>03</option>' . $n;
			$fieldString .= $t . '<option value="04"' . ($value == 4 ? 'selected="selected"' : '') . '>04</option>' . $n;
			$fieldString .= $t . '<option value="05"' . ($value == 5 ? 'selected="selected"' : '') . '>05</option>' . $n;
			$fieldString .= $t . '<option value="06"' . ($value == 6 ? 'selected="selected"' : '') . '>06</option>' . $n;
			$fieldString .= $t . '<option value="07"' . ($value == 7 ? 'selected="selected"' : '') . '>07</option>' . $n;
			$fieldString .= $t . '<option value="08"' . ($value == 8 ? 'selected="selected"' : '') . '>08</option>' . $n;
			$fieldString .= $t . '<option value="09"' . ($value == 9 ? 'selected="selected"' : '') . '>09</option>' . $n;
			$fieldString .= $t . '<option value="10"' . ($value == 10 ? 'selected="selected"' : '') . '>10</option>' . $n;
			$fieldString .= $t . '<option value="11"' . ($value == 11 ? 'selected="selected"' : '') . '>11</option>' . $n;
			$fieldString .= $t . '<option value="12"' . ($value == 12 ? 'selected="selected"' : '') . '>12</option>' . $n;
		}
		
		$fieldString .= '</select>' . $n;
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a day dropdown field.
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputAdvDateD($name = 'input_adv_date_d', $value = '', $properties = array()) {
		// Setup
		$n = self::NEWLINE;
		$t = self::TAB;
		
		// Build HTML
		$fieldString = '<select name="' . $name . '" id="' . $name . '" ' . self::getPropString($properties) . '>' . $n;
		$fieldString .= $t . '<option value="">--</option>' . $n;
		$fieldString .= $t . '<option value="01"' . ($value == 1 ? 'selected="selected"' : '') . '>01</option>' . $n;
		$fieldString .= $t . '<option value="02"' . ($value == 2 ? 'selected="selected"' : '') . '>02</option>' . $n;
		$fieldString .= $t . '<option value="03"' . ($value == 3 ? 'selected="selected"' : '') . '>03</option>' . $n;
		$fieldString .= $t . '<option value="04"' . ($value == 4 ? 'selected="selected"' : '') . '>04</option>' . $n;
		$fieldString .= $t . '<option value="05"' . ($value == 5 ? 'selected="selected"' : '') . '>05</option>' . $n;
		$fieldString .= $t . '<option value="06"' . ($value == 6 ? 'selected="selected"' : '') . '>06</option>' . $n;
		$fieldString .= $t . '<option value="07"' . ($value == 7 ? 'selected="selected"' : '') . '>07</option>' . $n;
		$fieldString .= $t . '<option value="08"' . ($value == 8 ? 'selected="selected"' : '') . '>08</option>' . $n;
		$fieldString .= $t . '<option value="09"' . ($value == 9 ? 'selected="selected"' : '') . '>09</option>' . $n;
		$fieldString .= $t . '<option value="10"' . ($value == 10 ? 'selected="selected"' : '') . '>10</option>' . $n;
		$fieldString .= $t . '<option value="11"' . ($value == 11 ? 'selected="selected"' : '') . '>11</option>' . $n;
		$fieldString .= $t . '<option value="12"' . ($value == 12 ? 'selected="selected"' : '') . '>12</option>' . $n;
		$fieldString .= $t . '<option value="13"' . ($value == 13 ? 'selected="selected"' : '') . '>13</option>' . $n;
		$fieldString .= $t . '<option value="14"' . ($value == 14 ? 'selected="selected"' : '') . '>14</option>' . $n;
		$fieldString .= $t . '<option value="15"' . ($value == 15 ? 'selected="selected"' : '') . '>15</option>' . $n;
		$fieldString .= $t . '<option value="16"' . ($value == 16 ? 'selected="selected"' : '') . '>16</option>' . $n;
		$fieldString .= $t . '<option value="17"' . ($value == 17 ? 'selected="selected"' : '') . '>17</option>' . $n;
		$fieldString .= $t . '<option value="18"' . ($value == 18 ? 'selected="selected"' : '') . '>18</option>' . $n;
		$fieldString .= $t . '<option value="19"' . ($value == 19 ? 'selected="selected"' : '') . '>19</option>' . $n;
		$fieldString .= $t . '<option value="20"' . ($value == 20 ? 'selected="selected"' : '') . '>20</option>' . $n;
		$fieldString .= $t . '<option value="21"' . ($value == 21 ? 'selected="selected"' : '') . '>21</option>' . $n;
		$fieldString .= $t . '<option value="22"' . ($value == 22 ? 'selected="selected"' : '') . '>22</option>' . $n;
		$fieldString .= $t . '<option value="23"' . ($value == 23 ? 'selected="selected"' : '') . '>23</option>' . $n;
		$fieldString .= $t . '<option value="24"' . ($value == 24 ? 'selected="selected"' : '') . '>24</option>' . $n;
		$fieldString .= $t . '<option value="25"' . ($value == 25 ? 'selected="selected"' : '') . '>25</option>' . $n;
		$fieldString .= $t . '<option value="26"' . ($value == 26 ? 'selected="selected"' : '') . '>26</option>' . $n;
		$fieldString .= $t . '<option value="27"' . ($value == 27 ? 'selected="selected"' : '') . '>27</option>' . $n;
		$fieldString .= $t . '<option value="28"' . ($value == 28 ? 'selected="selected"' : '') . '>28</option>' . $n;
		$fieldString .= $t . '<option value="29"' . ($value == 29 ? 'selected="selected"' : '') . '>29</option>' . $n;
		$fieldString .= $t . '<option value="30"' . ($value == 30 ? 'selected="selected"' : '') . '>30</option>' . $n;
		$fieldString .= $t . '<option value="31"' . ($value == 31 ? 'selected="selected"' : '') . '>31</option>' . $n;
		$fieldString .= '</select>' . $n;
		
		return $fieldString;
	}
	
	/**
	 * Assembles and returns the HTML required to display the specified field as
	 * a state dropdown field.
	 *
	 * @param string $name the name of the field
	 * @param string $value a predefined value for the field
	 * @param array $properties an array of element property/value pairs to add to the input tag
	 * @return string the assembled HTML
	 */
	public static function getInputAdvState($name = 'input_adv_state', $value = '', $properties = array()) {		
		// Set Options
		$options[] = array('label' => '--', 'value' => '');
		$options[] = array('label' => 'AL', 'value' => 'AL');
		$options[] = array('label' => 'AK', 'value' => 'AK');
		$options[] = array('label' => 'AZ', 'value' => 'AZ');
		$options[] = array('label' => 'AR', 'value' => 'AR');
		$options[] = array('label' => 'CA', 'value' => 'CA');
		$options[] = array('label' => 'CO', 'value' => 'CO');
		$options[] = array('label' => 'CT', 'value' => 'CT');
		$options[] = array('label' => 'DE', 'value' => 'DE');
		$options[] = array('label' => 'DC', 'value' => 'DC');
		$options[] = array('label' => 'FL', 'value' => 'FL');
		$options[] = array('label' => 'GA', 'value' => 'GA');
		$options[] = array('label' => 'HI', 'value' => 'HI');
		$options[] = array('label' => 'ID', 'value' => 'ID');
		$options[] = array('label' => 'IL', 'value' => 'IL');
		$options[] = array('label' => 'IN', 'value' => 'IN');
		$options[] = array('label' => 'IA', 'value' => 'IA');
		$options[] = array('label' => 'KS', 'value' => 'KS');
		$options[] = array('label' => 'KY', 'value' => 'KY');
		$options[] = array('label' => 'LA', 'value' => 'LA');
		$options[] = array('label' => 'ME', 'value' => 'ME');
		$options[] = array('label' => 'MD', 'value' => 'MD');
		$options[] = array('label' => 'MA', 'value' => 'MA');
		$options[] = array('label' => 'MI', 'value' => 'MI');
		$options[] = array('label' => 'MN', 'value' => 'MN');
		$options[] = array('label' => 'MS', 'value' => 'MS');
		$options[] = array('label' => 'MO', 'value' => 'MO');
		$options[] = array('label' => 'MT', 'value' => 'MT');
		$options[] = array('label' => 'NE', 'value' => 'NE');
		$options[] = array('label' => 'NV', 'value' => 'NV');
		$options[] = array('label' => 'NH', 'value' => 'NH');
		$options[] = array('label' => 'NJ', 'value' => 'NJ');
		$options[] = array('label' => 'NM', 'value' => 'NM');
		$options[] = array('label' => 'NY', 'value' => 'NY');
		$options[] = array('label' => 'NC', 'value' => 'NC');
		$options[] = array('label' => 'ND', 'value' => 'ND');
		$options[] = array('label' => 'OH', 'value' => 'OH');
		$options[] = array('label' => 'OK', 'value' => 'OK');
		$options[] = array('label' => 'OR', 'value' => 'OR');
		$options[] = array('label' => 'PA', 'value' => 'PA');
		$options[] = array('label' => 'RI', 'value' => 'RI');
		$options[] = array('label' => 'SC', 'value' => 'SC');
		$options[] = array('label' => 'SD', 'value' => 'SD');
		$options[] = array('label' => 'TN', 'value' => 'TN');
		$options[] = array('label' => 'TX', 'value' => 'TX');
		$options[] = array('label' => 'UT', 'value' => 'UT');
		$options[] = array('label' => 'VT', 'value' => 'VT');
		$options[] = array('label' => 'VA', 'value' => 'VA');
		$options[] = array('label' => 'WA', 'value' => 'WA');
		$options[] = array('label' => 'WV', 'value' => 'WV');
		$options[] = array('label' => 'WI', 'value' => 'WI');
		$options[] = array('label' => 'WY', 'value' => 'WY');		
		
		// Build HTML
		$fieldString = self::getInputSelect($name, $options, $value, $properties);
		
		return $fieldString;
	}
}
?>