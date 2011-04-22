<?php
/**
 * A column object representing a table column in the database. It stores
 * properties about the column and is typically stored within a
 * Bedrock_Model_Table object.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 08/29/2008
 * @updated 08/29/2008
 */
class Bedrock_Model_Column extends Bedrock_Model {
	const FK_TYPE_NONE = 0;
	const FK_TYPE_ONE_TO_ONE = 1;
	const FK_TYPE_MANY_TO_MANY = 2;
	const FK_TYPE_ONE_TO_MANY = 3;
	const FK_TYPE_MANY_TO_ONE = 4;
	
	const FIELD_TYPE_INT = 'INT';
	const FIELD_TYPE_FLOAT = 'FLOAT';
	const FIELD_TYPE_DOUBLE = 'DOUBLE';
	const FIELD_TYPE_DECIMAL = 'DECIMAL';
	const FIELD_TYPE_BOOL = 'BOOL';
	const FIELD_TYPE_VARCHAR = 'VARCHAR';
	const FIELD_TYPE_TEXT = 'TEXT';
	const FIELD_TYPE_BLOB = 'BLOB';
	const FIELD_TYPE_DATE = 'DATE';
	const FIELD_TYPE_TIME = 'TIME';
	const FIELD_TYPE_DATETIME = 'DATETIME';
	
	protected $_name;
	protected $_type;
	protected $_length;
	protected $_size;
	protected $_null;
	protected $_default;
	protected $_unique;
	protected $_key_primary;
	protected $_key_foreign;
	protected $_key_foreign_type;
	protected $_properties;
	protected $_defaults;
	
	/**
	 * Initializes the column object.
	 * 
	 * @param array $columnConfig the column's configuration
	 */
	public function __construct($columnConfig) {
		$this->_name = $columnConfig['name'];
		$this->_type = $this->stringToType(strtoupper($columnConfig['type']));
		$this->_length = $columnConfig['length'];
		$this->_size = $columnConfig['size'];
		$this->_null = $columnConfig['null'];
		$this->_default = $columnConfig['default'];
		$this->_key_primary = $columnConfig['primary_key'];
		$this->_key_foreign = $columnConfig['foreign_key'];
		$this->_key_foreign_type = $columnConfig['foreign_key_type'];
		$this->_properties = $columnConfig['properties'];
	}
	
	/**
	 * Returns the requested property.
	 *
	 * @param unknown_type $name
	 * @return unknown
	 */
	public function __get($name) {
		switch($name) {
			default:
				return NULL;
				break;
				
			case 'name':
				return $this->_name;
				break;
				
			case 'type':
				return $this->_type;
				break;
				
			case 'length':
				return $this->_length;
				break;
				
			case 'size':
				return $this->_size;
				break;
				
			case 'null':
				return $this->_null;
				break;
				
			case 'default':
				return $this->_default;
				break;
				
			case 'comment':
				return $this->_properties['comment'];
				break;
				
			case 'unique':
				return $this->_unique;
				break;
				
			case 'primary_key':
				return $this->_key_primary;
				break;
				
			case 'foreign_key':
				return $this->_key_foreign;
				break;
				
			case 'foreign_key_type':
				return $this->_key_foreign_type;
				break;
				
			case 'properties':
				return $this->_properties;
				break;
				
			case 'definition':
				return $this->definitionToString();
				break;
		}
	}
	
	/**
	 * Sets a custom property for the column (useful for properties not directly
	 * supported). If no value is specified, a default will be selected if
	 * available.
	 *
	 * @param string $propertyName the name of the property to set
	 * @param string $propertyValue the value for the property
	 */
	public function setProperty($propertyName, $propertyValue) {
		if($propertyValue == '') {
			$this->_properties[$propertyName] = $this->_defaults[$propertyName];
		}
		else {
			$this->_properties[$propertyName] = $propertyValue;
		}
	}
	
	/**
	 * Returns the column definition string.
	 *
	 * @return string the definition string for the column
	 */
	protected function definitionToString() {
		// Setup
		$definition = '';
		
		// Build Column Definition
		$definition .= '`' . $this->_name . '` ' . $this->typeToString();
		
		if($this->_properties['charset']) {
			$definition .= ' CHARACTER SET ' . self::sanitize($this->_properties['charset']);
		}
		
		if($this->_properties['collation']) {
			$definition .= ' COLLATE ' . self::sanitize($this->_properties['collation']);
		}
		
		if($this->_null) {
			$definition .= ' NULL';
		}
		else {
			$definition .= ' NOT NULL';
		}
		
		if($this->_default) {
			if($this->_type <= self::FIELD_TYPE_BOOL) {
				$definition .= ' DEFAULT ' . $this->_default;
			}
			elseif($this->_type <= self::FIELD_TYPE_DATETIME) {
				$definition .= ' DEFAULT \'' . self::sanitize($this->_default) . '\'';
			}
		}
		
		if($this->_properties['auto_increment']) {
			$definition .= ' AUTO_INCREMENT';
		}
		
		if($this->_properties['comment'] && !$this->_key_foreign) {
			$definition .= ' COMMENT \'' . self::sanitize($this->_properties['comment']) . '\'';
		}
		
		if($this->_key_foreign) {
			$definition .= ' COMMENT \'fk_' . self::sanitize($this->_key_foreign) . '\'';
		}
		
		return $definition;
	}
	
	/**
	 * Determines the proper specific data type for the currently set type
	 *
	 * @return string the corresponding data type
	 */
	public function typeToString() {
		// Setup
		$type = '';
		
		// Determine Type String
		switch($this->_type) {
			case self::FIELD_TYPE_INT:
				if($this->_size < 256) {
					$type = 'TINYINT(' . self::sanitize($this->_length) . ')';
				}
				elseif($this->_size < 65536) {
					$type = 'SMALLINT(' . self::sanitize($this->_length) . ')';
				}
				elseif($this->_size < 16777216) {
					$type = 'MEDIUMINT(' . self::sanitize($this->_length) . ')';
				}
				elseif($this->_size < 4294967296) {
					$type = 'INT(' . self::sanitize($this->_length) . ')';
				}
				elseif($this->_size < 18446744073709551616) {
					$type = 'BIGINT(' . self::sanitize($this->_length) . ')';
				}
				
				if($this->_properties['unsigned']) {
					$type .= ' UNSIGNED';
				}
				
				if($this->_properties['zerofill']) {
					$type .= ' ZEROFILL';
				}
				break;
				
			case self::FIELD_TYPE_FLOAT:
				$type = 'FLOAT';
				
				if($this->_properties['unsigned']) {
					$type .= ' UNSIGNED';
				}
				
				if($this->_properties['zerofill']) {
					$type .= ' ZEROFILL';
				}
				break;
				
			case self::FIELD_TYPE_DOUBLE:
				$type = 'DOUBLE(' . self::sanitize($this->_length) . ', ' . $this->_properties['decimals'] . ')';
				
				if($this->_properties['unsigned']) {
					$type .= ' UNSIGNED';
				}
				
				if($this->_properties['zerofill']) {
					$type .= ' ZEROFILL';
				}
				break;
				
			case self::FIELD_TYPE_DECIMAL:
				$type = 'DECIMAL(' . self::sanitize($this->_length) . ', ' . $this->_properties['decimals'] . ')';
				
				if($this->_properties['unsigned']) {
					$type .= ' UNSIGNED';
				}
				
				if($this->_properties['zerofill']) {
					$type .= ' ZEROFILL';
				}
				break;
				
			case self::FIELD_TYPE_BOOL:
				$type = 'BIT';
				break;
				
			case self::FIELD_TYPE_VARCHAR:
				$type = 'VARCHAR(' . self::sanitize($this->_length) . ')';
				break;
				
			case self::FIELD_TYPE_TEXT:
				if($this->_size < 256) {
					$type = 'TINYTEXT';
				}
				elseif($this->_size < 65536) {
					$type = 'TEXT';
				}
				elseif($this->_size < 16777216) {
					$type = 'MEDIUMTEXT';
				}
				elseif($this->_size < 4294967296) {
					$type = 'LONGTEXT';
				}
				break;
				
			case self::FIELD_TYPE_BLOB:
				if($this->_size < 256) {
					$type = 'TINYBLOB';
				}
				elseif($this->_size < 65536) {
					$type = 'BLOB';
				}
				elseif($this->_size < 16777216) {
					$type = 'MEDIUMBLOB';
				}
				elseif($this->_size < 4294967296) {
					$type = 'LONGBLOB';
				}
				break;
				
			case self::FIELD_TYPE_DATE:
				$type = 'DATE';
				break;
				
			case self::FIELD_TYPE_TIME:
				$type = 'TIME';
				break;
				
			case self::FIELD_TYPE_DATETIME:
				$type = 'DATETIME';
				break;
				
			default:
				throw new Bedrock_Model_Exception('Invalid data type "' . $this->_type . '" specified.');
				break;
		}
		
		return $type;
	}
	
	/**
	 * Attempts to convert a string value to a valid data type.
	 *
	 * @param string $typeString the string to match to a data type
	 * @return integer the resulting type, or a default if one could not be determined
	 */
	protected function stringToType($typeString) {
		switch(strtoupper($typeString)) {
			case 'TINYINT':
			case 'SMALLINT':
			case 'MEDIUMINT':
			case 'INT':
			case 'BIGINT':
				$type = self::FIELD_TYPE_INT;
				break;
				
			case 'FLOAT':
				$type = self::FIELD_TYPE_FLOAT;
				break;
				
			case 'DOUBLE':
				$type = self::FIELD_TYPE_DOUBLE;
				break;
				
			case 'DECIMAL':
				$type = self::FIELD_TYPE_DECIMAL;
				break;
				
			case 'BIT':
				$type = self::FIELD_TYPE_BOOL;
				break;
				
			default:
			case 'VARCHAR':
				$type = self::FIELD_TYPE_VARCHAR;
				break;
				
			case 'TINYTEXT':
			case 'TEXT':
			case 'MEDIUMTEXT':
			case 'LONGTEXT':
				$type = self::FIELD_TYPE_TEXT;
				break;
				
			case 'TINYBLOB':
			case 'BLOB':
			case 'MEDIUMBLOB':
			case 'LONGBLOB':
				$type = self::FIELD_TYPE_BLOB;
				break;
				
			case 'DATE':
				$type = self::FIELD_TYPE_DATE;
				break;
				
			case 'TIME':
				$type = self::FIELD_TYPE_TIME;
				break;
				
			case 'DATETIME':
				$type = self::FIELD_TYPE_DATETIME;
				break;
		}
		
		return $type;
	}
	
	/**
	 * Returns the column's foreign key type (if set) as a string.
	 *
	 * @return string a string representation of the foreign key type
	 */
	public function fkTypeToString() {
		// Setup
		$type = '';
		
		switch($this->_key_foreign_type) {
			case self::FK_TYPE_ONE_TO_ONE:
				$type = 'one_one';
				break;
				
			case self::FK_TYPE_ONE_TO_MANY:
				$type = 'one_many';
				break;
				
			case self::FK_TYPE_MANY_TO_ONE:
				$type = 'many_one';
				break;
				
			case self::FK_TYPE_MANY_TO_MANY:
				$type = 'many_many';
				break;
		}
		
		return $type;
	}
}
?>