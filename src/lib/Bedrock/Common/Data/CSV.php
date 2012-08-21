<?php
namespace Bedrock\Common\Data;

/**
 * Data Container: CSV (Comma Separated Values)
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 04/23/2009
 * @updated 07/02/2012
 */
class CSV extends \Bedrock\Common\Data {
	protected $_columns = array();
	private $_defaults = array(
		'column_headings' => false,
		'delimiter' => ','
	);

	/**
	 * Initializes the data object.
	 *
	 * @param mixed $data the data to load
	 * @param string $delimiter the delimiter separating the values
	 * @param boolean $columnHeadings whether or not to use column headings
	 */
	public function __construct($data = array(), $delimiter = ',', $columnHeadings = false) {
		if(is_array($data) || is_string($data)) {
			if(is_string($data)) {
				$data = self::decode($data, $delimiter, $columnHeadings);
			}

			if($columnHeadings) {
				$this->_columns = array_keys($data[0]);
			}
			
			parent::__construct($data);
			$this->optionSet('delimiter', $delimiter);
			$this->optionSet('column_headings', $columnHeadings);
		}
		else {
			parent::__construct($data);
		}
	}

	/**
	 * Returns the stored data as a CSV string.
	 *
	 * @return string the formatted stored data
	 */
	public function __toString() {
		return self::encode($this->_data, $this->optionGet('delimiter'), ($this->optionGet('column_headings') ? $this->_columns : array()));
	}

	/**
	 * Encodes the specified data into a string.
	 *
	 * @param mixed $data the data to encode
	 * @param string $delimiter the delimiter to use
	 * @param array $columns an optional array of column headings
	 * @return string the encoded data
	 */
	public static function encode($data, $delimiter = ',', $columns = array()) {
		// Setup
		$result = '';
		$columnCount = count($columns);
		
		if(is_array($data) || $data instanceof \Bedrock\Common\Data) {
			if($columnCount > 0) {
				$result .= implode($delimiter, $columns) . \Bedrock\Common::TXT_NEWLINE;
			}

			foreach($data as $row) {
				if($columnCount > 0) {
					foreach($columns as $column) {
						if(!array_key_exists($column, $row)) {
							$result .= $delimiter;
							continue;
						}
						else {
							$type = gettype($row[$column]);

							switch($type) {
								default:
									$result .= '"[' . $type . ']"';
									break;

								case 'string':
								case 'object':
									$result .= '"' . ((string) $row[$column]) . '"';
									break;

								case 'integer':
								case 'double':
									$result .= $row[$column];
									break;

								case 'boolean':
									$result .= $row[$column] ? 'TRUE' : 'FALSE';
									break;

								case 'null':
								case 'NULL':
									$result .= 'NULL';
									break;
							}

							$result .= $delimiter;
						}
					}
				}
				else {
					foreach($row as $value) {
						switch(gettype($value)) {
							default:
							case 'string':
							case 'object':
								$result .= '"' . ((string) $value) . '"';
								break;

							case 'integer':
							case 'double':
								$result .= $value;
								break;

							case 'boolean':
								$result .= $value ? 'TRUE' : 'FALSE';
								break;

							case 'null':
								$result .= 'NULL';
								break;
						}

						$result .= $delimiter;
					}
				}

				$result = substr($result, 0, -(strlen($delimiter))) . \Bedrock\Common::TXT_NEWLINE;
			}
		}
		else {
			throw new \Bedrock\Common\Data\Exception('The specified data is not in a supported format, please provide a valid array or Data descendant.');
		}

		return $result;
	}

	/**
	 * Decodes the specified string to an array.
	 *
	 * @param string $string the string to decode
	 * @param string $delimiter the delimiter to use
	 * @param boolean $columns whether or not the first line contains column headings
	 * @return array the decoded data
	 */
	public static function decode($string, $delimiter = ',', $columns = false) {
		// Setup
		$result = array();
		
		if(is_string($string)) {
			$rows = explode(\Bedrock\Common::TXT_NEWLINE, $string);
			$columnNames = null;
			$columnCount = null;
			
			if($columns) {
				$columnNames = array_shift($rows);
				$columnNames = explode($delimiter, $columnNames);
				$columnCount = count($columnNames);
			}
			
			foreach($rows as $key => $row) {
				if($columns) {
					if(trim($row == '')) {
						continue;
					}
					
					$row = explode($delimiter, $row);
					$rowCount = count($row);
					
					if($columnCount != $rowCount) {
						throw new \Bedrock\Common\Data\Exception('One or more rows dit not contain a matching number of values for the columns specified.');
					}
					
					$result[] = array_combine($columnNames, $row);
				}
				else {
					$result[] = explode($delimiter, $row);
				}

				foreach($columnNames as $column) {
					if(substr($result[$key][$column], 0, 1) == '"' && substr($result[$key][$column], -1) == '"') {
						$result[$key][$column] = substr($result[$key][$column], 1, -1);
					}
					elseif($result[$key][$column] == 'TRUE') {
						$result[$key][$column] = true;
					}
					elseif($result[$key][$column] == 'FALSE') {
						$result[$key][$column] = false;
					}
				}
			}
		}
		else {
			throw new \Bedrock\Common\Data\Exception('Only strings can be decoded, please provide a valid string to decode.');
		}
		
		return $result;
	}

	/**
	 * Resets the specified option to its default value.
	 *
	 * @param string $name the name of the option to reset
	 */
	public function optionReset($name = '') {
		if($name == '') {
			$this->_options = array_merge($this->_options, $this->_defaults);
			parent::optionReset($name);
		}
		elseif(array_key_exists($name, $this->_defaults)) {
			$this->_options[$name] = $this->_defaults[$name];
		}
		else {
			parent::optionReset($name);
		}
	}
}