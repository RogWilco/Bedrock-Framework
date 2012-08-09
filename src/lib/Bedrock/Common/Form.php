<?php
namespace Bedrock\Common;

/**
 * Manages form content and validation.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 12/15/2008
 * @updated 07/02/2012
 */
class Form extends \Bedrock implements \Bedrock\Common\Form\FormInterface {
	protected $_groups = array();
	protected $_fields = array();
	protected $_switches = array();
	protected static $_typeMappings = array(
		'std' => 'Bedrock\\Common\\Form\\Field\\Std',
		'adv' => 'Bedrock\\Common\\Form\\Field\\Adv'
	);
	
	/**
	 * Initializes a new form with the specified options.
	 *
	 * @param mixed $options additional options for the form object, either as an array, a Config object, or a string containing the path to an XML form definition file
	 */
	public function __construct($options = null) {
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
				'name' => 'form',
				'id' => 'form',
				'method' => 'post',
				'action' => $_SERVER['PHP_SELF']
			)), true);
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Loads form settings using the specified data. Data can either be supplied
	 * as an array, Config object, or a path to an XML form definition file.
	 *
	 * @param mixed $arg the data to load as an array, Config object, or a path to an XML form definition file
	 */
	public function load($arg) {
		try {
			// =================================================================
			// Argument: File Path
			// =================================================================
			if(is_string($arg)) {
				if(is_file($arg)) {
					// Load Form Data
					$data = simplexml_load_file($arg);
					
					// Store Properties
					$this->_properties->name = (string) $data->attributes()->name;
					$this->_properties->id = (string) $data->attributes()->id;
					$this->_properties->method = (string) $data->attributes()->method;
					$this->_properties->action = (string) $data->attributes()->action;
					
					if($this->_properties->id == '') {
						$this->_properties->id = $this->_properties->name;
					}
					
					// Store Children
					foreach($data->children() as $child) {
						switch($child->getName()) {
							default:
							case 'field':
								$this->storeField($child);
								break;
								
							case 'group':
								$this->storeGroup($child);
								break;
								
							case 'switch':
								$this->storeSwitch($child);
								break;
						}
					}
				}
				else {
					throw new \Bedrock\Common\Form\Exception('A valid form definition file was not found at "' . $arg . '"');
				}
			}
			
			// =================================================================
			// Argument: Array
			// =================================================================
			elseif(is_array($arg)) {
				// Store Properties
				$this->_properties->name = $arg['name'];
				$this->_properties->id = $arg['id'];
				$this->_properties->method = $arg['method'];
				$this->_properties->action = $arg['action'];
				
				if($this->_properties->id == '') {
					$this->_properties->id = $this->_properties->name;
				}
				
				// Store Children
				if(count($arg['children'])) {
					foreach($arg['children'] as $child) {
						switch($child['child_type']) {
							default:
							case 'field':
								$this->storeField($child);
								break;
								
							case 'group':
								$this->storeGroup($child);
								break;
								
							case 'switch':
								$this->storeSwitch($child);
								break;
						}
					}
				}
			}
			
			// =================================================================
			// Argument: Config Object
			// =================================================================
			elseif($arg instanceof \Bedrock\Common\Config) {
				// Store Properties
				$this->_properties->name = $arg->name;
				$this->_properties->id = $arg->id;
				$this->_properties->method = $arg->method;
				$this->_properties->action = $arg->action;
				
				if($this->_properties->id == '') {
					$this->_properties->id = $this->_properties->name;
				}
				
				// Store Children
				if(count($arg->children)) {
					foreach($arg->children as  $child) {
						switch($child->childType) {
							default:
							case 'field':
								$this->storeField($child);
								break;
								
							case 'group':
								$this->storeGroup($child);
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
				throw new \Bedrock\Common\Form\Exception('Invalid data type specified (' . gettype($arg) . '), valid types include array, string, Bedrock\Common\Config objects, or null.');
			}
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Gets the specified field's value.
	 *
	 * @param string $name the field whose value is to be returned
	 * @return mixed the corresponding value
	 */
	public function __get($name) {
		try {
			// Setup
			$result = null;
			
			// Check for Matching Group
			if(isset($this->_groups[$name])) {
				$result = $this->_groups[$name];
			}
			
			// Check for Matching Field
			elseif(isset($this->_fields[$name])) {
				$result = $this->_fields[$name];
			}
			
			// Check for Matching Field in Groups
			else {
				if(count($this->_groups)) {
					foreach($this->_groups as $group) {
						if($group->{$name}) {
							$result = $group->{$name};
						}
					}
				}
			}
			
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Sets the specified field value.
	 *
	 * @param string $name the name of the field for which the value will be set
	 * @param mixed $value the value to assign to the specified field
	 */
	public function __set($name, $value) {
		try {
			if($value instanceof \Bedrock\Common\Form\Field) {
				$this->_fields[$name] = $value;
			}
			elseif($value instanceof \Bedrock\Common\Form\Group) {
				$this->_groups[$name] = $value;
			}
			else {
				throw new \Bedrock\Common\Form\Exception('Only Field and Group objects can be assigned to a Form.');
			}
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Adds a new class-to-field-type mapping to the Form.
	 *
	 * @param string $type the new type to map
	 * @param string $className the class to map to
	 */
	public static function addMapping($type, $className) {
		try {
			self::$_typeMappings[$type] = $className;
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Retrieves the specified type/class mapping, or false if a mapping isn't
	 * found.
	 *
	 * @param string $type the type to search for
	 * @return string the name of the corresponding class
	 */
	public static function getMapping($type) {
		try {
			// Setup
			$result = false;
			
			if(array_key_exists($type, self::$_typeMappings)) {
				$result = self::$_typeMappings[$type];
			}
			
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Retrieves the requested Group by name or index, or returns all stored
	 * Groups if no index/namem is given.
	 *
	 * @param mixed $groupIndex either the index of the Group, or the Group's name
	 * @return mixed either the corresponding Group, all Groups, or null if none are found
	 */
	public function groups($groupIndex = null) {
		try {
			// Setup
			$result = null;
			
			if(is_string($groupIndex)) {
				$result = $this->_groups[$groupIndex];
			}
			elseif(is_int($groupIndex)) {
				$this->seek($groupIndex);
				
				if($this->current() instanceof \Bedrock\Common\Form\Group) {
					$result = $this->current();
				}
			}
			else {
				$result = $this->_groups;
			}
			
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Retrieves the requested Field by name or index, or returns all stored
	 * Fields if no index/name is given.
	 *
	 * @param mixed $fieldIndex either the index of the Field, or the Field's name
	 * @return mixed either the corresponding Field, all Fields, or null if none are found
	 */
	public function fields($fieldIndex = null) {
		try {
			// Setup
			$result = null;
			
			if(is_string($fieldIndex)) {
				$result = $this->_fields[$fieldIndex];
			}
			elseif(is_int($fieldIndex)) {
				$this->seek(count($this->_groups) + $fieldIndex);
				
				if($this->current() instanceof \Bedrock\Common\Form\Field) {
					$result = $this->current();
				}
			}
			else {
				$result = $this->_fields;
			}
			
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
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
		try {
			// Setup
			$result = null;
			
			if($switchIndex != null) {
				$result = $this->_switches[$switchIndex];
			}
			else {
				$result = $this->_switches;
			}
			
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Stores the specified field with the current instance.
	 *
	 * @param mixed $field the field data to store
	 */
	private function storeField($field) {
		try {
			// =================================================================
			// Source Data: SimpleXMLElement
			// =================================================================
			if($field instanceof \SimpleXMLElement) {
				if(!isset($field->attributes()->id) || $field->attributes()->id == '') {
					$field->addAttribute('id', $this->_properties->id . '_' . (string) $field->attributes()->name);
				}
				
				$type = '';
				list($type) = explode(':', (string) $field->attributes()->type);
				$fieldClass = self::getMapping($type);
				
				if($fieldClass) {
					$this->_fields[(string) $field->attributes()->name] =  new $fieldClass($field);
				}
				else {
					$this->_fields[(string) $field->attributes()->name] = new \Bedrock\Common\Form\Field($field);
				}
			}
			
			// =================================================================
			// Source Data: Config
			// =================================================================
			elseif($field instanceof \Bedrock\Common\Config) {
				if(!isset($field->id) || $field->id == '') {
					$field->id = $this->_properties->id . '_' . $field->name;
				}
				
				$type = '';
				list($type) = explode(':', (string) $field->type);
				$fieldClass = self::getMapping($type);
				
				if($fieldClass) {
					$this->_fields[$field->name] = new $fieldClass($field);
				}
				else {
					$this->_fields[$field->name] = new \Bedrock\Common\Form\Field($field);
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
				$fieldClass = self::getMapping($type);
				
				if($fieldClass) {
					$this->_fields[$field['name']] = new $fieldClass($field);
				}
				else {
					$this->_fields[$field['name']] = new \Bedrock\Common\Form\Field($field);
				}
			}
			
			// =================================================================
			// Source Data: Unsupported
			// =================================================================
			else {
				throw new \Bedrock\Common\Form\Exception('Invalid data provided, field could not be stored.');
			}
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Stores the specified group with the current instance.
	 *
	 * @param mixed $group the group data to store
	 */
	private function storeGroup($group) {
		try {
			// =================================================================
			// Source Data: SimpleXMLElement
			// =================================================================
			if($group instanceof \SimpleXMLElement) {
				if(!isset($group->attributes()->id) || $group->attributes()->id == '') {
					$group->addAttribute('id', $this->_properties->id . '_' . (string) $group->attributes()->name);
				}
				
				$this->_groups[(string) $group->attributes()->name] = new \Bedrock\Common\Form\Group($group);
			}
			
			// =================================================================
			// Source Data: Config
			// =================================================================
			elseif($group instanceof \Bedrock\Common\Config) {
				if(!isset($group->id) || $group->id == '') {
					$group->id = $this->_properties->id . '_' . $group->name;
				}
				
				$this->_groups[$group->name] = new \Bedrock\Common\Form\Group($group);
			}
			
			// =================================================================
			// Source Data: Array
			// =================================================================
			elseif(is_array($group)) {
				if(!isset($group['id']) || $group['id'] == '') {
					$group['id'] = $this->_properties->id . '_' . $group['name'];
				}
				
				$this->_groups[$group['name']] = new \Bedrock\Common\Form\Group($group);
			}
			
			// =================================================================
			// Source Data: Unsupported
			// =================================================================
			else {
				throw new \Bedrock\Common\Form\Exception('Invalid data provided, group could not be stored.');
			}
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Stores the specified switch block with the current instance.
	 *
	 * @param mixed $switch the switch data to store
	 */
	private function storeSwitch($switch) {
		try {
			// Setup
			$data = array();
			
			// =================================================================
			// Source Data: SimpleXMLElement
			// =================================================================
			if($switch instanceof \SimpleXMLElement) {
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
			elseif($switch instanceof \Bedrock\Common\Config) {
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
				throw new \Bedrock\Common\Form\Exception('Invalid data provided, switch block could not be stored.');
			}
			
			$this->_switches[] = new \Bedrock\Common\Config($data, true);
			
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Validates the current form and returns the result of the validation process.
	 *
	 * @return boolean whether or not the form passed validation
	 */
	public function validate() {
		try {
			// Setup
			$result = false;
			
			// Validate the Form
			
			
			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Returns whether or not the specified form Field or Group exists within the Form.
	 *
	 * @param int $offset the Field or Group to check
	 * @return boolean whether or not the Field or Group exists
	 */
	public function offsetExists($offset) {
		$result = false;
		
		if(is_numeric($offset) && ($this->_groups[$offset] || $this->_fields[$offset])) {
			$result = true;
		}
		
		return $result;
	}
	
	/**
	 * Returns the requested Field or Group from the Form.
	 *
	 * @param integer $offset the specified Field or Group to retrieve
	 * @return mixed the corresponding Field or Group object
	 */
	public function offsetGet($offset) {
		if(!is_numeric($offset)) {
			throw new \Bedrock\Common\Form\Exception('Fields and Groups can only be accessed using a numeric offset.');
		}
		elseif(!$this->offsetExists($offset)) {
			throw new \Bedrock\Common\Form\Exception('A Field or Group does not exist at the requested offset of ' . $offset);
		}
		
		if($this->_groups[$offset]) {
			return $this->_groups[$offset];
		}
		else {
			return $this->_fields[$offset];
		}
	}
	
	/**
	 * Sets the specified Field or Group to the specified offset within the Form.
	 *
	 * @param integer $offset the offset to use
	 * @param mixed $value the Field or Group to add
	 */
	public function offsetSet($offset, $value) {
		if(!is_numeric($offset)) {
			throw new \Bedrock\Common\Form\Exception('Fields and Groups can only be set using a numeric offset.');
		}
		elseif(!($value instanceof \Bedrock\Common\Form\Field) && !($value instanceof \Bedrock\Common\Form\Group)) {
			throw new \Bedrock\Common\Form\Exception('Attempted to add an unrecognized type to a Form object (only Field and Group objects are allowed).');
		}
		
		if($value instanceof \Bedrock\Common\Form\Group) {
			$this->_groups[$offset] = $value;
		}
		elseif($value instanceof \Bedrock\Common\Form\Field) {
			$this->_fields[$offset] = $value;
		}
	}
	
	/**
	 * Removes the specified Field or Group from the Form.
	 *
	 * @param integer $offset the offset to use
	 */
	public function offsetUnset($offset) {
		if(!is_numeric($offset)) {
			throw new \Bedrock\Common\Form\Exception('Fields or Groups can only be set using a numeric offset.');
		}
		
		if($this->_groups[$offset]) {
			unset($this->_groups[$offset]);
		}
		else {
			unset($this->_fields[$offset]);
		}
	}
	
	/**
	 * Returns the number of Fields currently stored in the Form, including all
	 * Field objects stored within child Group objects..
	 * 
	 * @return integer the number of Field objects
	 */
	public function count() {
		// Setup
		$result = 0;
		
		// Count Fields
		$result = count($this->_fields);
		
		foreach($this->_groups as $group) {
			$result += count($group);
		}
		
		return $result;
	}
	
	/**
	 * Returns the currently selected Field or Group object.
	 *
	 * @return mixed the Field or Group object currently selected
	 */
	public function current() {
		// Setup
		$result = current($this->_groups);
		
		if($result === false) {
			$result = current($this->_fields);
		}
		
		return $result;
	}
	
	/**
	 * Returns the key of the currently selected Field or Group object.
	 *
	 * @return integer the current key value
	 */
	public function key() {
		// Setup
		$result = key($this->_groups);
		
		if(current($this->_groups) === false) {
			$result = key($this->_fields);
		}
		
		return $result;
	}
	
	/**
	 * Advances the internal pointer to the next Field or Group in the Form.
	 * 
	 * @return mixed the next Field or Group in the Form
	 */
	public function next() {
		// Setup
		$result = false;
		
		if(current($this->_groups) === false) {
			$result = next($this->_fields);
		}
		else {
			$result = next($this->_groups);
			
			if($result === false) {
				$result = next($this->_fields);
			}
		}
		
		return $result;
	}
	
	/**
	 * Reverses the internal pointer to the previous Field or Group in the Form.
	 * 
	 * @return mixed the previous Field or Group in the Form
	 */
	public function prev() {
		// Setup
		$result = false;
		
		if(current($this->_groups) === false) {
			$result = prev($this->_fields);
			
			if($result === false) {
				$result = prev($this->_groups);
			}
		}
		else {
			$result = prev($this->_groups);
		}
		
		return $result;
	}
	
	/**
	 * Reverses the internal pointer to the first Field or Group in the Form.
	 */
	public function rewind() {
		reset($this->_groups);
		reset($this->_fields);
	}
	
	/**
	 * Returns the Field or Group object at the specified index.
	 *
	 * @param integer $index the index to seek
	 * @return mixed the corresponding Field or Group
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
			throw new \Bedrock\Common\Form\Exception('Invalid index specified.');
		}
		
		return $result;
	}
	
	/**
	 * Checks if the current element is valid after a call to the rewind() or
	 * next() functions.
	 *
	 * @return boolean whether or not the pointer currently points to a valid Field or Group
	 */
	public function valid() {
		return (current($this->_groups) !== false || current($this->_fields) !== false);
	}
}