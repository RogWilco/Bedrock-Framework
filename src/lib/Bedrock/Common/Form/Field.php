<?php
namespace Bedrock\Common\Form;

/**
 * Represents a form field, allowing for the modification of all available
 * properties.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 12/16/2008
 * @updated 07/02/2012
 */
class Field extends \Bedrock implements \Bedrock\Common\Form\Field\FieldInterface {
	protected $_value;
	protected $_rules;
	
	/**
	 * Initializes a new form field with the specified options.
	 *
	 * @param array $options additional objects for the field object
	 * @param string $parentId the parent form or group's ID
	 */
	public function __construct($options, $parentId = '') {
		try {
			// Load Default Properties
			$this->defaults();
			
			// Load Specified Options
			$this->load($options);
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Applies any default properties for the current object.
	 */
	public function defaults() {
		try {
			parent::defaults();
			
			$this->_properties->merge(new \Bedrock\Common\Config(array(
				'name' => 'field',
				'label' => 'Field',
				'type' => 'std',
				'subtype' => 'text'
			)), true);
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Loads field settings using the specified data. Data can either be
	 * supplied as an array, Config object, or a SimpleXMLElement object.
	 *
	 * @param mixed $arg the data to load as an array, Config object, or SimpleXMLElement object
	 */
	public function load($arg) {
		try {
			// =================================================================
			// Argument: SimpleXMLElement Object
			// =================================================================
			if($arg instanceof \SimpleXMLElement) {
				// Store Properties
				$this->_properties->name = (string) $arg->attributes()->name;
				$this->_properties->id = (string) $arg->attributes()->id;
				$this->_properties->label = (string) $arg->attributes()->label;
				$this->_properties->text = (string) $arg->attributes()->text;
				$this->_properties->type = (string) $arg->attributes()->type;
				$this->_value = (string) $arg->attributes()->value;
				
				if(isset($arg->options->option) && count($arg->options->option) > 0) {
					$count = count($arg->options->option);
					$options = array();
					
					for($i = 0; $i < $count; $i++) {
						$options[] = array('label' => $arg->options->option[$i], 'value' => $arg->options->option[$i]->attributes()->value);
					}
					
					$this->_properties->options = new \Bedrock\Common\Config($options);
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
					$this->_properties->options = new \Bedrock\Common\Config($arg['options']);
				}
			}
			
			// =================================================================
			// Argument: Config Object
			// =================================================================
			elseif($arg instanceof \Bedrock\Common\Config) {
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
				throw new \Bedrock\Common\Form\Exception('Invalid data type specified (' . gettype($arg) . '), valid types include array, string, Bedrock\Common\Config objects, or null.');
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
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Gets the specified property's value.
	 *
	 * @param string $name the name of the desired property
	 * @return mixed the corresponding value
	 */
	public function __get($name) {
		try {
			// Setup
			$result = null;
			
			if($name == 'value') {
				$result = $this->_value;
			}
			elseif(isset($this->_properties->{$name})) {
				$result = $this->_properties->{$name};
			}
			
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Sets the specified property to the specified value.
	 *
	 * @param string $name the name of the property to set
	 * @param string $value the value to apply to the property
	 */
	public function __set($name, $value) {
		try {
			if($name == 'value') {
				$this->_value = $value;
			}
			else {
				$this->_properties->{$name} = $value;
			}
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Renders the specified property.
	 *
	 * @param string $property the name of the property to render
	 */
	public function render($property = 'input') {
		try {
			switch($property) {
				default:
					echo $this->__get($property);
					break;
					
				case 'input':
					throw new \Bedrock\Common\Form\Exception('Cannot render field input, field type is unspecified.');
					break;
			}
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Prints the specified attribute if a value is specified.
	 *
	 * @param string $attribute the attribute to print
	 * @param string $value the value to associate with the attribute
	 *
	 * @return string the resulting attribute string
	 */
	protected function attributeToString($attribute, $value) {
		try {
			// Setup
			$result = '';
			
			if($value != '') {
				$result = $attribute . '="' . $value . '" ';
			}
			
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
}