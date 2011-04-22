<?php
/**
 * A container for logically grouping related form fields. Useful for multi-page
 * and extremely large forms.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 12/19/2008
 * @updated 12/19/2008
 */
class Bedrock_Common_Form_Group extends Bedrock implements Bedrock_Common_Form_Group_Interface {
	protected $_fields = array();
	protected $_switches = array();
	
	/**
	 * Initializes the field group.
	 *
	 * @param mixed $options initialization options for the field group
	 */
	public function __construct($options = null) {
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
				'name' => 'group',
				'label' => 'Group'
			)), true);
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Loads group settings using the specified data. Data can either be supplied
	 * as an array, Config object, or a SimpleXMLElement object.
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
				
				// Store Children
				foreach($arg->children() as $child) {
					switch($child->getName()) {
						default:
						case 'field':
							$this->storeField($child);
							break;
							
						case 'switch':
							$this->storeSwitch($child);
							break;
					}
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
				
				// Store Children
				if(count($arg['children'])) {
					foreach($arg['children'] as $child) {
						switch($child['child_type']) {
							default:
							case 'field':
								$this->storeField($child);
								break;
								
							case 'switch':
								$this->storeSwitch($field);
								break;
						}
					}
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
				
				// Store Children
				if(count($arg->children)) {
					foreach($arg->children as  $child) {
						switch($child->childType) {
							default:
							case 'field':
								$this->storeField($child);
								break;
								
							case 'switch':
								$this->storeSwitch($child);
								break;
						}
					}
				}
			}
			
			// =================================================================
			// Argument: Invalid
			// =================================================================
			elseif($arg != null) {
				throw new Bedrock_Common_Form_Exception('Invalid data type specified (' . gettype($arg) . '), valid types include array, string, Bedrock_Common_Config objects, or null.');
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Gets the specified field's value.
	 *
	 * @param string $name the field whose value is to be returned
	 * @return mixed the corresponding value
	 */
	public function __get($name) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = null;
			
			// Check for Matching Field
			if(isset($this->_fields[$name])) {
				$result = $this->_fields[$name];
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
	 * Sets the specified field value.
	 *
	 * @param string $name the name of the field for which the value will be set
	 * @param mixed $value the value to assign to the specified field
	 */
	public function __set($name, $value) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			if($value instanceof Bedrock_Common_Form_Field) {
				$this->_fields[$name] = $value;
			}
			else {
				throw new Bedrock_Common_Form_Exception('Only Field and Group objects can be assigned to a Form.');
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Retrieves the requested Field by name or index, or returns all stored
	 * Fields if no index/name is given.
	 *
	 * @param mxied $fieldIndex either the index of the Field, or the Field's name
	 * @return mixed either the corresponding Field, all Fields, or null if none are found
	 */
	public function fields($fieldIndex = null) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = null;
			
			if(is_string($fieldIndex)) {
				$result = $this->_fields[$fieldIndex];
			}
			elseif(is_int($fieldIndex)) {
				$result = $this->seek($fieldIndex);
			}
			else {
				$result = $this->_fields;
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
	 * Retrieves the requested switch block by index, or returns all stored
	 * switch blocks if no index is given.
	 *
	 * @param integer $switchIndex the index of the switch block entry
	 * @return array either the corresponding switch block, all switch blocks, or null if none are found
	 */
	public function switches($switchIndex = null) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = null;
			
			if($switchIndex != null) {
				$result = $this->_switches[$switchIndex];
			}
			else {
				$result = $this->_switches;
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
	 * Stores the specified field with the current instance.
	 *
	 * @param mixed $field the field data to store
	 */
	private function storeField($field) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// =================================================================
			// Source Data: SimpleXMLElement
			// =================================================================
			if($field instanceof SimpleXMLElement) {
				if(!isset($field->attributes()->id) || $field->attributes()->id == '') {
					$field->addAttribute('id', $this->_properties->id . '_' . (string) $field->attributes()->name);
				}
				
				$type = '';
				list($type) = explode(':', (string) $field->attributes()->type);
				$fieldClass = Bedrock_Common_Form::getMapping($type);
				
				if($fieldClass) {
					$this->_fields[(string) $field->attributes()->name] =  new $fieldClass($field);
				}
				else {
					$this->_fields[(string) $field->attributes()->name] = new Bedrock_Common_Form_Field($field);
				}
			}
			
			// =================================================================
			// Source Data: Config
			// =================================================================
			elseif($field instanceof Bedrock_Common_Config) {
				if(!isset($field->id) || $field->id == '') {
					$field->id = $this->_properties->id . '_' . $field->name;
				}
				
				$type = '';
				list($type) = explode(':', (string) $field->type);
				$fieldClass = Bedrock_Common_Form::getMapping($type);
				
				if($fieldClass) {
					$this->_fields[$field->name] = new $fieldClass($field);
				}
				else {
					$this->_fields[$field->name] = new Bedrock_Common_Form_Field($field);
				}
			}
			
			// =================================================================
			// Source Data: Array
			// =================================================================
			elseif(is_array($field)) {
				if(!isset($field['id']) || $field['id'] == '') {
					$field['id'] = $this->_properties->id . '_' . $field['name'];
				}
				
				$type = '';
				list($type) = explode(':', (string) $field['type']);
				$fieldClass = Bedrock_Common_Form::getMapping($type);
				
				if($fieldClass) {
					$this->_fields[$field['name']] = new $fieldClass($field);
				}
				else {
					$this->_fields[$field['name']] = new Bedrock_Common_Form_Field($field);
				}
			}
			
			// =================================================================
			// Source Data: Unsupported
			// =================================================================
			else {
				throw new Bedrock_Common_Form_Exception('Invalid data provided, field could not be stored.');
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Stores the specified switch block with the current instance.
	 *
	 * @param mixed $switch the switch data to store
	 */
	private function storeSwitch($switch) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$data = array();
			
			// =================================================================
			// Source Data: SimpleXMLElement
			// =================================================================
			if($switch instanceof SimpleXMLElement) {
				// Setup
				$data = array(
					'depends' => $switch->attributes()->depends,
					'cases' => array()
				);
				
				// Store Cases
				foreach($switch->case as $case) {
					foreach($case->field as $field) {
						// Store Field
						$this->storeField($field);
						
						// Store Case Details
						$data['cases'][] = array(
							'target' => $case->attributes()->target,
							'field' => $field->attributes()->id
						);
					}
				}
			}
			
			// =================================================================
			// Source Data: Config
			// =================================================================
			elseif($switch instanceof Bedrock_Common_Config) {
				// Setup
				$data = array(
					'depends' => $switch->depends,
					'cases' => array()
				);
				
				// Store Cases
				foreach($switch->cases as $case) {
					foreach($case->fields as $field) {
						// Store Field
						$this->storeField($field);
						
						// Store Case Details
						$data['cases'][] = array(
							'target' => $case->target,
							'field' => $field->id
						);
					}
				}
			}
			
			// =================================================================
			// Source Data: Array
			// =================================================================
			elseif(is_array($switch)) {
				// Setup
				$data = array(
					'depends' => $switch['depends'],
					'cases' => array()
				);
				
				// Store Cases
				foreach($switch['cases'] as $case) {
					foreach($case['fields'] as $field) {
						// Store Field
						$this->storeField($field);
						
						// Store Case Details
						$data['cases'][] = array(
							'target' => $case->target,
							'field' => $field->id
						);
					}
				}
			}
			
			// =================================================================
			// Source Data: Unsupported
			// =================================================================
			else {
				throw new Bedrock_Common_Form_Exception('Invalid data provided, switch block could not be stored.');
			}
			
			$this->_switches[] = new Bedrock_Common_Config($data, true);
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	/**
	 * Returns whether or not the specified form Field exists within the Group.
	 *
	 * @param int $offset the Field to check
	 * @return boolean whether or not the Field exists
	 */
	public function offsetExists($offset) {
		$result = false;
		
		if(is_numeric($offset) && $this->_fields[$offset]) {
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * Returns the requested Field from the Group.
	 *
	 * @param integer $offset the specified Field to retrieve
	 * @return mixed the corresponding Field object
	 */
	public function offsetGet($offset) {
		if(!is_numeric($offset)) {
			throw new Bedrock_Common_Form_Exception('Fields can only be accessed using a numeric offset.');
		}
		elseif(!$this->offsetExists($offset)) {
			throw new Bedrock_Common_Form_Exception('A Field does not exist at the requested offset of ' . $offset);
		}
		
		return $this->_fields[$offset];
	}
	
	/**
	 * Sets the specified Field to the specified offset within the Group.
	 *
	 * @param integer $offset the offset to use
	 * @param mixed $value the Field to add
	 */
	public function offsetSet($offset, $value) {
		if(!is_numeric($offset)) {
			throw new Bedrock_Common_Form_Exception('Fields can only be set using a numeric offset.');
		}
		elseif(!($value instanceof Bedrock_Common_Form_Field)) {
			throw new Bedrock_Common_Form_Exception('Attempted to add an unrecognized type to a Groupo object (only Field objects are allowed).');
		}
		
		$this->_fields[$offset] = $value;
	}
	
	/**
	 * Removes the specified Field from the Group.
	 *
	 * @param integer $offset the offset to use
	 */
	public function offsetUnset($offset) {
		if(!is_numeric($offset)) {
			throw new Bedrock_Common_Form_Exception('Fields can only be set using a numeric offset.');
		}
		
		unset($this->_fields[$offset]);
	}
	
	/**
	 * Returns the number of Fields currently stored in the Group.
	 * 
	 * @return integer the number of Field objects
	 */
	public function count() {
		return count($this->_fields);
	}
	
	/**
	 * Returns the currently selected Field object.
	 *
	 * @return Bedrock_Common_Form_Field the Field object currently selected
	 */
	public function current() {
		return current($this->_fields);
	}
	
	/**
	 * Returns the key of the currently selected Field object.
	 *
	 * @return integer the current key value
	 */
	public function key() {
		return key($this->_fields);
	}
	
	/**
	 * Advances the internal pointer to the next Field in the Group.
	 * 
	 * @return mixed the next Field in the Group
	 */
	public function next() {
		return next($this->_fields);
	}
	
	/**
	 * Reverses the internal pointer to the previous Field in the Group.
	 * 
	 * @return mixed the previous Field in the Group
	 */
	public function prev() {
		return prev($this->_fields);
	}
	
	/**
	 * Reverses the internal pointer to the first Field in the Group.
	 */
	public function rewind() {
		reset($this->_fields);
	}
	
	/**
	 * Returns the Field object at the specified index.
	 *
	 * @param integer $index the index to seek
	 * @return mixed the corresponding Field
	 */
	public function seek($index) {
		// Setup
		$result = null;
		$this->rewind();
		$position = 0;
		
		while($position < $index && $this->valid()) {
			$result = $this->next();
			$position++;
		}
		
		if(!$this->valid()) {
			throw new Bedrock_Common_Form_Exception('Invalid index specified.');
		}
		
		return $result;
	}
	
	/**
	 * Checks if the current element is valid after a call to the rewind() or
	 * next() functions.
	 *
	 * @return boolean whether or not the pointer currently points to a valid Field
	 */
	public function valid() {
		return (current($this->_fields) !== false);
	}
}
?>