<?php
namespace Bedrock;

/**
 * Base view object, used to represent application output.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.1
 * @created 
 * @updated 09/07/2020
 */
abstract class View extends \Bedrock {
	/**
	 * The default constructor.
	 *
	 * @return \Bedrock\View
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Set the values to use when displaying the view.
	 *
	 * @param string $name the name of the value
	 * @param mixed $value the value to use
	 * @param mixed $default a default value if the passed value is empty
	 *
	 * @return void
	 */
	public function setValue($name, $value, $default = '') {
		if(gettype($value) != 'object') {
			\Bedrock\Common\Logger::info('Value Set: ' . $name . ' = ' . $value);
		}
		else {
			\Bedrock\Common\Logger::info('Value Set: ' . $name . ' = Object');
		}

		if(!isset($value) || count($value) == 0) {
			if(is_array($value)) {
				$value = array();
			}
			else {
				$value = $default;
			}
		}

		$this->_values[$name] = $value;
	}

	/**
	 * Returns the requested value.
	 *
	 * @param string $name the name of the value to return
	 *
	 * @return mixed the requested value
	 */
	public function getValue($name) {
		return $this->_values[$name];
	}

	/**
	 * Sets the specified value.
	 *
	 * @param string $name the name of the value
	 * @param mixed $value the value to store
	 */
	public function __set($name, $value) {
		$this->setValue($name, $value);
	}

	/**
	 * Returns the requested value.
	 *
	 * @param string $name the name of the value to return
	 *
	 * @return mixed the requested value
	 */
	public function __get($name) {
		return $this->getValue($name);
	}

	abstract function render();
}