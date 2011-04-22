<?php
/**
 * Represents a form field, allowing for the modification of all available
 * properties.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 12/16/2008
 * @updated 12/16/2008
 */
class Bedrock_Common_Form_Field extends Bedrock implements Bedrock_Common_Form_Field_Interface {
	protected $_value;
	protected $_rules;
	
	/**
	 * Initializes a new form field with the specified options.
	 *
	 * @param array $options additional objects for the field object
	 * @param string $parentId the parent form or group's ID
	 */
	public function __construct($options, $parentId = '') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Load Default Properties
			$this->defaults();
			
			// Load Specified Options
			$this->load($options);
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Applies any default properties for the current object.
	 */
	public function defaults() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			parent::defaults();
			
			$this->_properties->merge(new Bedrock_Common_Config(array(
				'name' => 'field',
				'label' => 'Field',
				'type' => 'std',
				'subtype' => 'text'
			)), true);
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Loads field settings using the specified data. Data can either be
	 * supplied as an array, Config object, or a SimpleXMLElement object.
	 *
	 * @param mixed $arg the data to load as an array, Config object, or SimpleXMLElement object
	 */
	public function load($arg) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// =================================================================
			// Argument: SimpleXMLElement Object
			// =================================================================
			if($arg instanceof SimpleXMLElement) {
				// Store Properties
				$this->_properties->name = (string) $arg->attributes()->name;
				$this->_properties->id = (string) $arg->attributes()->id;
				$this->_properties->label = (string) $arg->attributes()->label;
				$this->_properties->text = (string) $arg->attributes()->text;
				$this->_properties->type = (string) $arg->attributes()->type;
				$this->_value = (string) $arg->attributes()->value;
				
				if(isset($arg->options->option) && count($arg->options->option) > 0) {
					$count = count($arg->options->option);
					
					for($i = 0; $i < $count; $i++) {
						$options[] = array('label' => $arg->options->option[$i], 'value' => $arg->options->option[$i]->attributes()->value);
					}
					
					$this->_properties->options = new Bedrock_Common_Config($options);
				}
			}
			
			// =================================================================
			// Argument: Array
			// =================================================================
			elseif(is_array($arg)) {
				// Store Properties
				$this->_properties->name = $arg['name'];
				$this->_properties->id = $arg['id'];
				$this->_properties->label = $arg['label'];
				$this->_properties->text = $arg['text'];
				$this->_properties->type = $arg['type'];
				$this->_value = $arg['value'];
				
				if(isset($arg['options'])) {
					$this->_properties->options = new Bedrock_Common_Config($arg['options']);
				}
			}
			
			// =================================================================
			// Argument: Config Object
			// =================================================================
			elseif($arg instanceof Bedrock_Common_Config) {
				// Store Properties
				$this->_properties->name = $arg->name;
				$this->_properties->id = $arg->id;
				$this->_properties->label = $arg->label;
				$this->_properties->text = $arg->text;
				$this->_properties->type = $arg->type;
				$this->_value = $arg->value;
				$this->_properties->options = $arg->options;
			}
			
			// =================================================================
			// Argument: Invalid
			// =================================================================
			elseif($arg != null) {
				throw new Bedrock_Common_Form_Exception('Invalid data type specified (' . gettype($arg) . '), valid types include array, string, Bedrock_Common_Config objects, or null.');
			}
			
			// Update Type
			$typeData = explode(':', $this->_properties->type);
			
			if(count($typeData) <= 1) {
				$this->_properties->type = 'std';
				$this->_properties->subtype = $typeData[0];
			}
			else {
				$this->_properties->type = $typeData[0];
				$this->_properties->subtype = $typeData[1];
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Gets the specified property's value.
	 *
	 * @param string $name the name of the desired property
	 * @return mixed the corresponding value
	 */
	public function __get($name) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = null;
			
			if($name == 'value') {
				$result = $this->_value;
			}
			elseif(isset($this->_properties->{$name})) {
				$result = $this->_properties->{$name};
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Sets the specified property to the specified value.
	 *
	 * @param string $name the name of the property to set
	 * @param string $value the value to apply to the property
	 */
	public function __set($name, $value) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($name == 'value') {
				$this->_value = $value;
			}
			else {
				$this->_properties->{$name} = $value;
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Renders the specified property.
	 *
	 * @param string $property the name of the property to render
	 */
	public function render($property = 'input') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			switch($property) {
				default:
					echo $this->__get($property);
					break;
					
				case 'input':
					throw new Bedrock_Common_Form_Exception('Cannot render field input, field type is unspecified.');
					break;
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Prints the specified attribute if a value is specified.
	 *
	 * @param string $attribute the attribute to print
	 * @param string $value the value to associate with the attribute
	 */
	protected function attributeToString($attribute, $value) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			
			if($value != '') {
				$result = $attribute . '="' . $value . '" ';
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
}
?>