<?php
/**
 * Basic application logger, providing simple 3-level logging capabilities.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 2.0.0
 * @created 06/09/2008
 * @updated 06/09/2008
 */
class Bedrock_Common_Logger extends Bedrock {
	const LEVEL_ERROR = 4;
	const LEVEL_WARN = 3;
	const LEVEL_INFO = 2;
	const LEVEL_TRAVERSE = 1;
	const LEVEL_DEFAULT = 0;
	
	const OUTPUT_STRING = 'string';
	const OUTPUT_ARRAY = 'array';
	
	const TIME_FORMAT = 'Y-m-d H:i:s.u';
	const TIME_SECOND_DIVISONS = 3;
	
	const TYPE_EVENT = 0;
	const TYPE_EXCEPTION = 1;
	const TYPE_TABLE = 2;
	const TYPE_ENTRY = 3;
	const TYPE_EXIT = 4;
	
	protected static $_log;
	protected static $_level;
	protected static $_meta;
	protected static $_targets = array();
	protected static $_levels = array();
	
	/**
	 * Initializes the logger, with optional values for application-specific
	 * details.
	 *
	 * @param integer $level the verbosity level
	 * @param string $title the title for the application
	 * @param string $version the version of the application
	 * @param string $status the status of the application
	 */
	public static function init($level = self::LEVEL_ERROR, $title = 'Bedrock Web Application', $version = '0.0.0', $status = 'DEV') {
		self::$_meta['title'] = $title;
		self::$_meta['version'] = $version;
		self::$_meta['status'] = $status;
		self::$_meta['time'] = self::getTime();
		
		switch($level) {
			case self::LEVEL_TRAVERSE:
				self::$_level = self::LEVEL_TRAVERSE;
				break;
			case self::LEVEL_INFO:
				self::$_level = self::LEVEL_INFO;
				break;
			case self::LEVEL_WARN:
				self::$_level = self::LEVEL_WARN;
				break;
			default:
			case self::LEVEL_ERROR:
				self::$_level = self::LEVEL_ERROR;
				break;
		}
	}
	
	/**
	 * Returns the current log as an associative array.
	 *
	 * @return array the current log contents
	 */
	public static function toArray() {
		return self::$_log;
	}
	
	/**
	 * Adds a target object for log data to be written to.
	 *
	 * @param Bedrock_Common_Logger_Target $targetObj a target object
	 * @param mixed $level the logging level to use for the target
	 */
	public static function addTarget($targetObj, $level = self::LEVEL_DEFAULT) {
		self::$_targets[] = $targetObj;

		if(is_string($level)) {
			$level = self::stringToLevel($level);
		}
		
		if($level === self::LEVEL_DEFAULT) {
			self::$_levels[] = self::$_level;
		}
		else {
			self::$_levels[] = $level;
		}
	}
	
	/**
	 * Returns the specified logging level as a user-readable string.
	 *
	 * @param integer $level a valid logging level
	 * @return string a user-readable string of the logging level
	 */
	public static function levelToString($level) {
		switch($level) {
			case self::LEVEL_TRAVERSE:
				$result = 'TRAVERSE';
				break;
			
			default:
			case self::LEVEL_INFO:
				$result = 'INFO';
				break;
				
			case self::LEVEL_WARN:
				$result = 'WARN';
				break;
				
			case self::LEVEL_ERROR:
				$result = 'ERROR';
				break;
		}
		
		return $result;
	}

	/**
	 * Translates a string to the corresponding logging level.
	 *
	 * @param string $string the string to convert
	 * @return integer the corresponding logging level value
	 */
	public static function stringToLevel($string) {
		$string = strtoupper(trim($string));

		switch($string) {
			case 'TRAVERSE':
				$result = self::LEVEL_TRAVERSE;
				break;

			default:
			case 'INFO':
				$result = self::LEVEL_INFO;
				break;

			case 'WARN':
				$result = self::LEVEL_WARN;
				break;

			case 'ERROR':
				$result = self::LEVEL_ERROR;
				break;
		}

		return $result;
	}
	
	/**
	 * Retrieves an associative array conntaining the calling function name and
	 * class name.
	 * 
	 * @return array an associative array containing the calling function and class names
	 */
	protected static function getCaller() {
		$backtrace = debug_backtrace();
		
		$result['class'] = $backtrace[2]['class'];
		$result['function'] = $backtrace[2]['function'];
		$result['args'] = $backtrace[2]['args'];
		
		if($result['class'] == '') {
			$result['class'] = 'global';
		}
		
		if($result['function'] == '' ||
				$result['function'] == 'include' ||
				$result['function'] == 'include_once' ||
				$result['function'] == 'require' ||
				$result['function'] == 'require_once') {
			$result['function'] = 'main';
		}
		
		return $result;
	}
	
	/**
	 * Writes a log entry array to all currently stored targets.
	 *
	 * @param array $logArray a log entry array
	 */
	protected static function writeToTargets($logArray) {
		// Output to Targets
		foreach(self::$_targets as $key => $target) {
			// Start of Log Session
			if(count(self::$_log) == 1) {
				switch($target->getFormat()) {
					default:
					case self::OUTPUT_STRING:
						$n = Bedrock_Common::TXT_NEWLINE;
						
						$logString = $n . $n . $n . '----- ' . self::$_meta['title'] .
							' PHP Log | ' . self::$_meta['status'] .
							' Version ' . self::$_meta['version'] .
							' | Script Execution: ' . self::$_meta['time'] .
							' -----' . $n . $n;
						
						$target->write($logString);
						break;
						
					case self::OUTPUT_ARRAY:
						//$targetArray[0] = '';
						//$targetArray[1] = self::levelToString(self::LEVEL_INFO);
						
						//$target->write(array('main' => array()));
						break;
				}
			}
			
			// Log Entry
			if(self::$_levels[$key] <= $logArray['level']) {
				$message = $logArray['msg'];
				
				// Handle Special Data Types
				if($logArray['type'] == self::TYPE_TABLE) {
					if(is_object($logArray['msg'])) {
						$message = self::objectToArray($logArray['msg']);
					}
					elseif(is_array($logArray['msg']) && is_object($logArray['msg'][1])) {
						$message = self::objectToArray($logArray['msg'][1], $logArray['msg'][0]);
					}
				}
				
				switch($target->getFormat()) {
					default:
					case self::OUTPUT_STRING:
						$n = Bedrock_Common::TXT_NEWLINE;
						
						if($logArray['type'] == self::TYPE_EXCEPTION && is_object($logArray['msg'])) {
							$message = get_class($logArray['msg']) . ': ' . $logArray['msg']->getMessage();
						}
						
						$entryString = $logArray['time'] . ' || ' . self::levelToString($logArray['level']) . ' || ' . $logArray['class'] . ' -- ' . $logArray['function'] . ' - ' . $message . $n;
						$target->write($entryString);
						break;
						
					case self::OUTPUT_ARRAY:
						$targetArray[0] = $message;
						$targetArray[1] = self::levelToString($logArray['level']);
						$targetArray[2] = $logArray['class'];
						$targetArray[3] = $logArray['function'];
						$targetArray[4] = $logArray['type'];
						
						$target->write($targetArray);
						break;
				}
			}
		}
	}
	
	/**
	 * Adds an entry to the log.
	 *
	 * @param string $msg the message for the log entry
	 * @param integer $level the logging level to use
	 * @param integer $type the type of entry to log
	 */
	public static function log($msg, $level = self::LEVEL_INFO, $type = self::TYPE_EVENT) {
		$caller = self::getCaller();
		
		$logArray = array('msg' => $msg,
								'time' => self::getTime(),
								'level' => $level,
								'type' => $type,
								'class' => $caller['class'],
								'function' => $caller['function']);
		
		if($level <= self::$_level) {
			self::$_log[] = $logArray;
		}
		
		self::writeToTargets($logArray);
	}
	
	/**
	 * Logs an INFO level entry.
	 *
	 * @param string $msg the message to include with the log entry
	 * @param integer $type the type of entry to log
	 */
	public static function info($msg, $type = self::TYPE_EVENT) {
		$caller = self::getCaller();
		
		$logArray = array('msg' => $msg,
								'time' => self::getTime(),
								'level' => self::LEVEL_INFO,
								'type' => $type,
								'class' => $caller['class'],
								'function' => $caller['function']);
		
		if(self::LEVEL_INFO <= self::$_level) {
			self::$_log[] = $logArray;
		}
		
		self::writeToTargets($logArray);
	}
	
	/**
	 * Logs a WARN level entry.
	 *
	 * @param string $msg the message to include with the log entry
	 * @param integer $type the type of entry to log
	 */
	public static function warn($msg, $type = self::TYPE_EVENT) {
		$caller = self::getCaller();
		
		$logArray = array('msg' => $msg,
								'time' => self::getTime(),
								'level' => self::LEVEL_WARN,
								'type' => $type,
								'class' => $caller['class'],
								'function' => $caller['function']);
		
		if(self::LEVEL_WARN <= self::$_level) {
			self::$_log[] = $logArray;
		}
		
		self::writeToTargets($logArray);
	}
	
	/**
	 * Logs an ERROR level entry.
	 *
	 * @param string $msg the message to include with the log entry
	 * @param integer $type the type of entry to log
	 */
	public static function error($msg, $type = self::TYPE_EVENT) {
		$caller = self::getCaller();
		
		$logArray = array('msg' => $msg,
								'time' => self::getTime(),
								'level' => self::LEVEL_ERROR,
								'type' => $type,
								'class' => $caller['class'],
								'function' => $caller['function']);
		
		if(self::LEVEL_ERROR <= self::$_level) {
			self::$_log[] = $logArray;
		}
		
		self::writeToTargets($logArray);
	}
	
	/**
	 * Logs an ERROR level Exception.
	 *
	 * @param $ex the Exception to log
	 */
	public static function exception($ex) {
		$caller = self::getCaller();
		
		$logArray = array('msg' => $ex,
								'time' => self::getTime(),
								'level' => self::LEVEL_ERROR,
								'type' => self::TYPE_EXCEPTION,
								'class' => $caller['class'],
								'function' => $caller['function']);
		
		if(self::LEVEL_ERROR <= self::$_level) {
			self::$_log[] = $logArray;
		}
		
		self::writeToTargets($logArray);
	}
	
	/**
	 * Logs an INFO level table.
	 *
	 * @param string $title a title for the table
	 * @param array $data the data to display in the table
	 */
	public static function table($title, $data) {
		$caller = self::getCaller();
		
		$logArray = array('msg' => array($title, $data),
							'time' => self::getTime(),
							'level' => self::LEVEL_INFO,
							'type' => 'table',
							'class' => $caller['class'],
							'function' => $caller['function']);
		
		if(self::LEVEL_INFO <= self::$_level) {
			self::$_log[] = $logArray;
		}
		
		self::writeToTargets($logArray);
	}
	
	/**
	 * Logs an entry into a process.
	 */
	public static function logEntry() {
		$caller = self::getCaller();
		
		$logArray = array('msg' => '__ENTRY__',
								'time' => self::getTime(),
								'level' => self::LEVEL_TRAVERSE,
								'type' => self::TYPE_ENTRY,
								'class' => $caller['class'],
								'function' => $caller['function'],
								'args' => $caller['args']);
		
		if(self::LEVEL_TRAVERSE <= self::$_level) {
			self::$_log[] = $logArray;
		}
		
		self::writeToTargets($logArray);
	}
	
	/**
	 * Logs an exit from a process.
	 */
	public static function logExit() {
		$caller = self::getCaller();
		
		$logArray = array('msg' => '__EXIT__',
								'time' => self::getTime(),
								'level' => self::LEVEL_TRAVERSE,
								'type' => self::TYPE_EXIT,
								'class' => $caller['class'],
								'function' => $caller['function']);
		
		if(self::LEVEL_TRAVERSE <= self::$_level) {
			self::$_log[] = $logArray;
		}
		
		self::writeToTargets($logArray);
	}
	
	/**
	 * Returns the current timestamp.
	 * 
	 * @return string the formatted timestamp according to the set time format
	 */
	protected static function getTime() {
		$time = explode(' ', microtime());
		
		if(self::TIME_FORMAT == 'Y-m-d H:i:s.u') {
			return date('Y-m-d H:i:s', $time[1]) . '.' . str_pad(floor($time[0] * pow(10, self::TIME_SECOND_DIVISONS)), self::TIME_SECOND_DIVISONS, '0', STR_PAD_LEFT);
		}
		elseif(self::TIME_FORMAT == 'H:i:s.u') {
			return date('H:i:s', $time[1]) . '.' . str_pad(floor($time[0] * pow(10, self::TIME_SECOND_DIVISONS)), self::TIME_SECOND_DIVISONS, '0', STR_PAD_LEFT);
		}
		else {
			return date(self::TIME_FORMAT, $time[1]);
		}
	}
	
	/**
	 * Converts compatible objects to a loggable array.
	 *
	 * @param mixed $object the object to convert
	 * @param string $title if a title is associated with the object
	 */
	protected static function objectToArray($object, $title = '') {
		// Setup
		$result = array();
		
		switch(get_class($object)) {
			case 'Bedrock_Model_ResultSet':
				if(!$title) {
					$title = 'ResultSet: ' . $object->count() . ' Records';
				}
				
				if($object->count()) {
					$columns = array();
					
					// Loop Through ResultSet
					foreach($object as $key => $record) {
						$row = array();
						
						// Get Column Values, Column Names on First Loop
						foreach($record as $column => $value) {
							$row[] = $value;
							
							if($key == 0) {
								$columns[] = $column;
							}
						}
						
						if($key == 0) {
							$result[] = $columns;
						}
						
						$result[] = $row;
					}
				}
				break;
				
			case 'Bedrock_Model_Record':
				if(!$title) {
					$title = 'Record: ' . $object->getTable()->getProperty('name');
				}
				
				$result[] = array('Column', 'Value');
				
				foreach($object as $column => $value) {
					$result[] = array($column, $value);
				}
				
				break;
				
			case 'Bedrock_Model_Database':
				$tables = $object->getTables();
				$config = $object->getConfig();
				
				if(!$title) {
					$title = 'Database Connection: ' . $config->params->dbname . '@' . $config->params->host;
				}
				
				$result[] = array('Table', 'Type', 'Rows', 'Auto Increment', 'Data Length', 'Engine', 'Collation', 'Create Time', 'Update Time');
				
				foreach($tables as $table) {
					$result[] = array($table->getProperty('name'), $table->getProperty('type'), $table->getProperty('rows'), $table->getProperty('auto_increment'), $table->getProperty('length'), $table->getProperty('engine'), $table->getProperty('collation'), $table->getProperty('created'), $table->getProperty('updated'));
				}
				
				break;
				
			case 'Bedrock_Model_Table':
				if(!$title) {
					$title = 'Table: ' . $object->getProperty('name');
				}
				
				$result[] = array('Column', 'Type', 'Length', 'Default', 'Null');
				$columns = $object->getColumns();
				
				foreach($columns as $column) {
					$result[] = array($column->name, $column->typeToString(), $column->length, $column->default, $column->null);
				}
				
				break;
				
			case 'Bedrock_Model_Column':
				if(!$title) {
					$title = 'Column: ' . $object->name;
				}
				
				$result[] = array('Property', 'Value');
				
				$result[] = array('Name', $object->name);
				$result[] = array('Type', $object->typeToString());
				$result[] = array('Length', $object->length);
				$result[] = array('Size', $object->size);
				$result[] = array('Null', $object->null);
				$result[] = array('Default', $object->default);
				$result[] = array('Primary Key', $object->primaryKey);
				$result[] = array('Foreign Key', $object->foreignKey);
				$result[] = array('Foreign Key Type', '');
				$result[] = array('Properties', $object->properties);
				
				break;
				
			default:
				// Incompatible Object
				break;
		}
		
		$result = array($title, $result);
		
		return $result;
	}
}
?>