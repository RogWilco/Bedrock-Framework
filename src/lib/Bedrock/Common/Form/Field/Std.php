<?php
/**
 * Standard form fields for base XHTML input types.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 12/25/2008
 * @updated 12/25/2008
 *
 */
class Bedrock_Common_Form_Field_Std extends Bedrock_Common_Form_Field {
	/**
	 * Renders the specified property or input field.
	 *
	 * @param string $property the property to render
	 */
	public function render($property = 'input') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$namespace = '';
			$type = '';
			$html = '';
			
			if($property != 'input') {
				parent::render($property);
			}
			else {
				list($namespace, $type) = explode(':', $this->__get('type'));
				
				if($namespace != 'std') {
					throw new Bedrock_Common_Form_Exception('The specified type namespace "' . $namespace . '" does not match the field instance type.');
				}
				
				switch($type) {
					// =========================================================
					// Type: Unsupported
					// =========================================================
					default:
						throw new Bedrock_Common_Form_Exception('The specified field type "' . $type . '" is not a recognized field type.');
						break;
						
					// =========================================================
					// Type: Basic Input Types
					// =========================================================
					case 'text':
					case 'password':
					case 'hidden':
					case 'checkbox':
					case 'radio':
					case 'button':
					case 'submit':
					case 'reset':
						$html = '<input type="' . $type .
									'" name="' . $this->__get('name') .
									'" id="' . $this->__get('id') . '" ' .
									$this->attributeToString('value', $this->_value) .
									$this->attributeToString('tabindex', $this->__get('tabindex')) .
									$this->attributeToString('size', $this->__get('size')) .
									$this->attributeToString('maxlength', $this->__get('maxlength')) .
									$this->attributeToString('class', $this->__get('class')) .
									$this->attributeToString('style', $this->__get('style')) . 
									$this->attributeToString('disabled', ($this->__get('disabled') ? 'disabled' : '')) . '/>' . "\n";
						
						if($type == 'checkbox' || $type == 'radio') {
							$html .= '<label for="' . $this->__get('id') . '">' . $this->__get('text') . '</label>' . "\n";
						}
						break;
						
					// =========================================================
					// Type: New Password (Passwor Field + Confirmation Field)
					// =========================================================
					case 'newpassword':
						$html = '<input type="password" name="' . $this->__get('name') . '_a' .
									'" id="' . $this->__get('id') . '_a" ' .
									$this->attributeToString('value', $this->_value) .
									$this->attributeToString('tabindex', $this->__get('tabindex')) .
									$this->attributeToString('size', $this->__get('size')) .
									$this->attributeToString('maxlength', $this->__get('maxlength')) .
									$this->attributeToString('class', $this->__get('class')) .
									$this->attributeToString('style', $this->__get('style')) . 
									$this->attributeToString('disabled', ($this->__get('disabled') ? 'disabled' : '')) . '/>' . "\n";
						
						$html .= '<br />';
						
						$html .= '<input type="password" name="' . $this->__get('name') . '_b' .
									'" id="' . $this->__get('id') . '_b" ' .
									$this->attributeToString('value', $this->_value) .
									$this->attributeToString('tabindex', $this->__get('tabindex') + 1) .
									$this->attributeToString('size', $this->__get('size')) .
									$this->attributeToString('maxlength', $this->__get('maxlength')) .
									$this->attributeToString('class', $this->__get('class')) .
									$this->attributeToString('style', $this->__get('style')) . 
									$this->attributeToString('disabled', ($this->__get('disabled') ? 'disabled' : '')) . '/>' . "\n";
						break;
						
					// =========================================================
					// Type: Textarea Box
					// =========================================================
					case 'textarea':
						$html = '<textarea name="' . $this->__get('name') . '" id="' . $this->_get('id') . '" >' . $this->_value . '</textarea>' . "\n";
						break;
						
					// =========================================================
					// Type: File Selection
					// =========================================================
					case 'file':
						$html = '<input type="file" name="' . $this->__get('name') .
									'" id="' . $this->__get('id') . '" ' .
									$this->attributeToString('value', $this->_value) .
									$this->attributeToString('tabindex', $this->__get('tabindex')) .
									$this->attributeToString('class', $this->__get('class')) .
									$this->attributeToString('style', $this->__get('style')) .
									$this->attributeToString('accept', $this->__get('accept')) .
									$this->attributeToString('disabled', ($this->__get('disabled') ? 'disabled' : '')) . '/>' . "\n";
						break;
						
					// =========================================================
					// Type: Select Dropdown
					// =========================================================
					case 'select':
						$html = '<select name="' . $this->__get('name') .
									'" id="' . $this->__get('id') . '" ' .
									$this->attributeToString('tabindex', $this->__get('tabindex')) .
									$this->attributeToString('size', $this->__get('size')) .
									$this->attributeToString('multiple', ($this->__get('multiple') ? 'multiple' : '')) .
									$this->attributeToString('class', $this->__get('class')) .
									$this->attributeToString('style', $this->__get('style')) . 
									$this->attributeToString('disabled', ($this->__get('disabled') ? 'disabled' : '')) . '>' . "\n";
									
						if(count($this->__get('options'))) {
							foreach($this->__get('options') as $option) {
								$html .= '<option value="' . $option->value . '"' . ($this->_value == $option->value ? ' selected="selected"' : '') . '>' . $option->label . '</option>' . "\n";
							}
						}
									
						$html .= '</select>' . "\n";
						
						break;
						
					// =========================================================
					// Type: Multi-Checkbox List
					// =========================================================
					case 'multicheck':
						if(count($this->__get('options'))) {
							foreach($this->__get('options') as $key => $option) {
								$html .= '<input type="checkbox" name="' . $this->__get('name') .
											'" id="' . $this->__get('id') . '_' . $key . '" ' .
											'" value="' . $option->value . '" ' .
											$this->attributeToString('checked', in_array($option->value, explode(',', $this->_value)) ? 'checked' : '') .
											$this->attributeToString('class', $this->__get('class')) .
											$this->attributeToString('style', $this->__get('style')) .
											$this->attributeToString('disabled', ($this->__get('disabled') ? 'disabled' : '')) . '/>' .
											'<label for="' . $this->__get('id') . '_' . $key . '">' . $option->label . '</label><br />' . "\n";
							}
						}
						break;
						
					// =========================================================
					// Type: Multi-Radio List
					// =========================================================
					case 'multiradio':
						if(count($this->__get('options'))) {
							foreach($this->__get('options') as $key => $option) {
								$html .= '<input type="radio" name="' . $this->__get('name') .
											'" id="' . $this->__get('id') . '_' . $key . '" ' .
											'" value="' . $option->value . '" ' .
											$this->attributeToString('checked', $option->value == $this->_value ? 'checked' : '') .
											$this->attributeToString('class', $this->__get('class')) .
											$this->attributeToString('style', $this->__get('style')) .
											$this->attributeToString('disabled', ($this->__get('disabled') ? 'disabled' : '')) . '/>' .
											'<label for="' . $this->__get('id') . '_' . $key . '">' . $option->label . '</label><br />' . "\n";
							}
						}
						break;
				}
				
				echo $html;
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
}
?>