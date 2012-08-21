<?php
namespace Bedrock\Common;

/**
 * Hadles communication with the FirePHP Firefox extension.
 * 
 * *** BEGIN LICENSE BLOCK *****
 *  
 * This file is part of FirePHP (http://www.firephp.org/).
 * 
 * Software License Agreement (New BSD License)
 * 
 * Copyright (c) 2006-2008, Christoph Dorn
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * 
 *     * Redistributions of source code must retain the above copyright notice,
 *       this list of conditions and the following disclaimer.
 * 
 *     * Redistributions in binary form must reproduce the above copyright notice,
 *       this list of conditions and the following disclaimer in the documentation
 *       and/or other materials provided with the distribution.
 * 
 *     * Neither the name of Christoph Dorn nor the names of its
 *       contributors may be used to endorse or promote products derived from this
 *       software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * ***** END LICENSE BLOCK *****
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 10/19/2008
 * @updated 07/02/2012
 */
class FirePHP extends \Bedrock {
	const VERSION = '0.2.b.7';
	const TYPE_LOG = 'LOG';
	const TYPE_INFO = 'INFO';
	const TYPE_WARN = 'WARN';
	const TYPE_ERROR = 'ERROR';
	const TYPE_DUMP = 'DUMP';
	const TYPE_TRACE = 'TRACE';
	const TYPE_EXCEPTION = 'EXCEPTION';
	const TYPE_TABLE = 'TABLE';
	const TYPE_GROUP_START = 'GROUP_START';
	const TYPE_GROUP_END = 'GROUP_END';
	
	protected static $_instance = NULL;
	protected $_messageIndex = 1;
	protected $_options = array();
	protected $_objectStack = array();
	private $_json_objectStack = array();
	
	/**
	 * Initializes the FirePHP object.
	 */
	public function __construct() {
		$this->_options['maxObjectDepth'] = 10;
		$this->_options['maxArrayDepth'] = 20;
		$this->_options['useNativeJsonEncode'] = true;
		$this->_options['includeLineNumbers'] = true;
	}
	
	/**
	 * Resets the message index if the object is unserialized.
	 */
	public function __wakeup() {
		$this->_messageIndex = 1;
	}
	
	/**
	 * Retrieves the current instance, or creates one if one has not been
	 * initialized.
	 *
	 * @param boolean $autoCreate whether or not to create an instance if none is found
	 * @return \Bedrock\Common\FirePHP the current instance
	 */
	public static function getInstance($autoCreate = false) {
		if($autoCreate && !self::$_instance) {
			self::init();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Initializes a new object instance.
	 *
	 * @return \Bedrock\Common\FirePHP a new instance
	 */
	public static function init() {
		return self::$_instance = new self();
	}
	
	/**
	 * Applies the specified options.
	 * 
	 * Options:
	 *  - maxObjectDepth: The maximum depth to traverse objects (default: 10)
	 *  - maxArrayDepth: The maximum depth to traverse arrays (default: 20)
	 *  - useNativeJsonEncode: If true will use json_encode() (default: true)
	 *  - includeLineNumbers: If true will include line numbers and filenames (default: true)
	 *
	 * @param array $options the options to apply
	 */
	public function setOptions($options) {
		$this->_options = array_merge($this->_options, $options);
	}
	
	/**
	 * Sets FirePHP as the error handler. Will throw PHP errors as exceptions.
	 * 
	 * The following errors will not be caught by this:
	 *  E_ERROR, E_PARSE, E_CORE_ERROR,
	 *  E_CORE_WARNING, E_COMPILE_ERROR,
	 *  E_COMPILE_WARNING, E_STRICT
	 */
	public function registerErrorHandler() {
  		set_error_handler(array($this, 'errorHandler'));     
	}
	
	/**
	 * FirePHP's error handler.
	 *
	 * @param int $errno the error number
	 * @param string $errstr the error message
	 * @param string $errfile the file in which the error occurred
	 * @param int $errline the line on which the error occurred
	 * @param array $errcontext the context within which the error occurred
	 */
	public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
		// Don't throw exception if error reporting is switched off
		if(error_reporting() == 0) {
			return;
		}
		
		// Only throw exceptions for errors we are asking for
		if(error_reporting() & $errno) {
			throw new \Bedrock\Common\Error\Exception($errstr, 0, $errno, $errfile, $errline);
		}
	}
	
	/**
	 * Registers FirePHP as the exception handler.
	 */
	public function registerExceptionHandler() {
		set_exception_handler(array($this, 'exceptionHandler'));
	}
	
	/**
	 * FirePHP's exception handler.
	 *
	 * @param Exception $exception
	 */
	function exceptionHandler($exception) {
		$this->fb($exception);
	}
	
	/**
	 * Sets the Processor URL
	 *
	 * @param string $url the processor URL to use
	 */
	public function setProcessorUrl($url) {
		$this->setHeader('X-FirePHP-ProcessorURL', $url);
	}
	
	/**
	 * Sets the Renderer URL
	 *
	 * @param string $url the renderer URL to use
	 */
	public function setRendererUrl($url) {
		$this->setHeader('X-FirePHP-RendererURL', $url);
	}
	
	/**
	 * Starts a group for the messages that follow.
	 *
	 * @param string $name a name for the group
	 * @return boolean whether or not the grouping was successful
	 */
	public function group($name) {
		return $this->fb(NULL, $name, self::TYPE_GROUP_START);
	}
	
	/**
	 * Ends a previously started group.
	 *
	 * @return boolean whether or not the grouping was successful
	 */
	public function groupEnd() {
		return $this->fb(NULL, NULL, self::TYPE_GROUP_END);
	}
	
	/**
	 * Logs an object with a label to the Firebug console.
	 *
	 * @param mixed $object the data to log
	 * @param string $label the label to use
	 * @return boolean whether or not the data was logged
	 */
	public function log($object, $label = NULL) {
		return $this->fb($object, $label, self::TYPE_LOG);
	}
	
	/**
	 * Logs an object with a label to the Firebug console.
	 *
	 * @param mixed $object the data to log
	 * @param string $label the label to use
	 * @return boolean whether or not the data was logged
	 */
	public function info($object, $label = NULL) {
		return $this->fb($object, $label, self::TYPE_INFO);
	}
	
	/**
	 * Logs an object with a label to the Firebug console.
	 *
	 * @param mixed $object the data to log
	 * @param string $label the label to use
	 * @return boolean whether or not the data was logged
	 */
	public function warn($object, $label = NULL) {
		return $this->fb($object, $label, self::TYPE_WARN);
	}
	
	/**
	 * Logs an object with a label to the Firebug console.
	 *
	 * @param mixed $object the data to log
	 * @param string $label the label to use
	 * @return boolean whether or not the data was logged
	 */
	public function error($object, $label = NULL) {
		return $this->fb($object, $label, self::TYPE_ERROR);
	}
	
	/**
	 * Dumps the key and variable to the Firbug server panel.
	 *
	 * @param string $key the key to dump
	 * @param string $variable the corresponding variable
	 * @return boolean whether or not the data was dumped
	 */
	public function dump($key, $variable) {
		return $this->fb($variable, $key, self::TYPE_DUMP);
	}
	
	/**
	 * Logs a trace to the Firebug console.
	 *
	 * @param string $label a label for the trace
	 * @return boolean whether or not the trace was logged
	 */
	public function trace($Label) {
		return $this->fb($Label, self::TYPE_TRACE);
	}
	
	/**
	 * Logs a table to the Firebug console.
	 *
	 * @param string $label a label for the table
	 * @param string $table the contents of the table
	 * @return boolean wether or not the table was logged
	 */
	public function table($label, $table) {
		return $this->fb($table, $label, self::TYPE_TABLE);
	}
	
	/**
	 * Determines if the connected client has the FirePHP extension installed
	 * and activated.
	 *
	 * @return boolean the result of the check
	 */
	public function detectClientExtension() {
		// Verify FirePHP is installed on the client.
		$m = array();
		
		if(!@preg_match_all('/\sFirePHP\/([\.|\d]*)\s?/si', $this->getUserAgent(), $m) ||
			!version_compare($m[1][0], '0.0.6', '>=')) {
			return false;
		}
	
		return true;
	}
	
	/**
	 * Sends the specified log data to the FirePHP extension running on the
	 * client.
	 *
	 * @param mixed $object the log data to be sent
	 * @return boolean the result of the process
	 */
	public function fb($object) {
		// Setup
		$type = NULL;
		$label = NULL;
		$meta = array();
		$filename = '';
		$linenum = '';
		
		if(headers_sent($filename, $linenum)) {
			throw new \Bedrock\Common\Exception('Headers already sent in ' . $filename . ' on line ' . $linenum . '. Cannot send log data to FirePHP. You must have Output Buffering enabled via ob_start() or output_buffering ini directive.');
		}
		
		if(func_num_args() == 1) {}
		elseif(func_num_args() == 2) {
			switch(func_get_arg(1)) {
				case self::TYPE_LOG:
				case self::TYPE_INFO:
				case self::TYPE_WARN:
				case self::TYPE_ERROR:
				case self::TYPE_DUMP:
				case self::TYPE_TRACE:
				case self::TYPE_EXCEPTION:
				case self::TYPE_TABLE:
				case self::TYPE_GROUP_START:
				case self::TYPE_GROUP_END:
					$type = func_get_arg(1);
					break;
					
				default:
					$label = func_get_arg(1);
					break;
			}
		}
		elseif(func_num_args()==3) {
			$type = func_get_arg(2);
			$label = func_get_arg(1);
		}
		else {
			throw new \Bedrock\Common\Exception('Wrong number of arguments to fb() function.');
		}
		
		if(!$this->detectClientExtension()) {
			return false;
		}
		
		if($object instanceof Exception) {
			$meta['file'] = $this->escapeTraceFile($object->getFile());
			$meta['line'] = $object->getLine();
			$trace = $object->getTrace();
			
			if($object instanceof \Bedrock\Common\Exception
					&& isset($trace[0]['function'])
					&& $trace[0]['function'] == 'errorHandler'
					&& isset($trace[0]['class'])
					&& $trace[0]['class'] == 'FirePHP') {
				$severity = false;
				
				switch($object->getSeverity()) {
					case E_WARNING:
						$severity = 'E_WARNING';
						break;
						
					case E_NOTICE:
						$severity = 'E_NOTICE';
						break;
						
					case E_USER_ERROR:
						$severity = 'E_USER_ERROR';
						break;
						
					case E_USER_WARNING:
						$severity = 'E_USER_WARNING';
						break;
						
					case E_USER_NOTICE:
						$severity = 'E_USER_NOTICE';
						break;
						
					case E_STRICT:
						$severity = 'E_STRICT';
						break;
						
					case E_RECOVERABLE_ERROR:
						$severity = 'E_RECOVERABLE_ERROR';
						break;
						
					case E_DEPRECATED:
						$severity = 'E_DEPRECATED';
						break;
						
					case E_USER_DEPRECATED:
						$severity = 'E_USER_DEPRECATED';
						break;
				}
				
				$object = array(
							'Class' => get_class($object),
							'Message' => $severity . ': ' . $object->getMessage(),
							'File' => $this->_escapeTraceFile($object->getFile()),
							'Line' => $object->getLine(),
							'Type' => 'trigger',
							'Trace' => $this->_escapeTrace(array_splice($trace, 2))
				);
			}
			else {
				$object = array(
							'Class' => get_class($object),
							'Message' => $object->getMessage(),
							'File' => $this->escapeTraceFile($object->getFile()),
							'Line' => $object->getLine(),
							'Type' => 'throw',
							'Trace' => $this->escapeTrace($object->getTrace())
				);
			}
			
			$type = self::TYPE_EXCEPTION;
		}
		elseif($type == self::TYPE_TRACE) {
			$trace = debug_backtrace();
			
			if(!$trace) return false;
			
			for($i=0; $i<sizeof($trace); $i++) {
				if(isset($trace[$i]['class'])
						&& isset($trace[$i]['file'])
						&& ($trace[$i]['class'] == 'Bedrock\\Common\\FirePHP'
							|| $trace[$i]['class'] == 'Bedrock\\Common\\Stream\\Output\\FirePHP'
							|| $trace[$i]['class'] == 'Bedrock\\Common\\Logger')
						&& ((substr($this->standardizePath($trace[$i]['file']), -11, 11) == 'FirePHP.php')
							|| (substr($this->standardizePath($trace[$i]['file']), -11, 11) == 'FirePHP.php')
							|| (substr($this->standardizePath($trace[$i]['file']), -10, 10) == 'Logger.php'))) {
					// Skip Self/Logger
				}
				elseif($trace[$i]['function'] == 'fb'
						|| $trace[$i]['function'] == 'trace'
						|| $trace[$i]['function'] == 'send') {
					$object = array(
								'Class' => isset($trace[$i]['class']) ? $trace[$i]['class'] : '',
								'Type' => isset($trace[$i]['type']) ? $trace[$i]['type'] : '',
								'Function' => isset($trace[$i]['function']) ? $trace[$i]['function'] : '',
								'Message' => $trace[$i]['args'][0],
								'File' => isset($trace[$i]['file']) ? $this->escapeTraceFile($trace[$i]['file']) : '',
								'Line' => isset($trace[$i]['line']) ? $trace[$i]['line'] : '',
								'Args' => isset($trace[$i]['args']) ? $trace[$i]['args'] : '',
								'Trace' => $this->escapeTrace(array_splice($trace, $i+1))
					);
					
					$meta['file'] = isset($trace[$i]['file']) ? $this->escapeTraceFile($trace[$i]['file']) : '';
					$meta['line'] = isset($trace[$i]['line']) ? $trace[$i]['line'] : '';
					break;
				}
			}
		}
		elseif($type === NULL) {
			$type = self::TYPE_LOG;
		}
		
		if($this->_options['includeLineNumbers']) {
			if(!isset($meta['file']) || !isset($meta['line'])) {
				$trace = debug_backtrace();
				$trace = debug_backtrace();
				
				for($i=0; $trace && $i<sizeof($trace); $i++) {
					if(isset($trace[$i]['class'])
							&& isset($trace[$i]['file'])
							&& ($trace[$i]['class'] == 'Bedrock\\Common\\FirePHP'
								|| $trace[$i]['class'] == 'Bedrock\\Common\\Stream\\Output\\FirePHP'
								|| $trace[$i]['class'] == 'Bedrock\\Common\\Logger')
							&& ((substr($this->standardizePath($trace[$i]['file']), -11, 11) == 'FirePHP.php')
								|| (substr($this->standardizePath($trace[$i]['file']), -11, 11) == 'FirePHP.php')
								|| (substr($this->standardizePath($trace[$i]['file']), -10, 10) == 'Logger.php'))) {
						// Skip Self/Logger
					}
					else {
						$meta['file'] = isset($trace[$i]['file']) ? $this->escapeTraceFile($trace[$i]['file']) : '';
						$meta['line'] = isset($trace[$i]['line']) ? $trace[$i]['line'] : '';
						break;
					}
				}
			}
		}
		else {
			unset($meta['file']);
			unset($meta['line']);
		}
		
		$this->setHeader('X-Wf-Protocol-1', 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2');
		$this->setHeader('X-Wf-1-Plugin-1', 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/' . self::VERSION);
		
		$structure_index = 1;
		
		if($type == self::TYPE_DUMP) {
			$structure_index = 2;
			$this->setHeader('X-Wf-1-Structure-2','http://meta.firephp.org/Wildfire/Structure/FirePHP/Dump/0.1');
		}
		else {
			$this->setHeader('X-Wf-1-Structure-1','http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1');
		}
		
		if($type==self::TYPE_DUMP) {
			$msg = '{"' . $label . '":' . $this->jsonEncode($object) . '}';
		}
		else {
			$msg_meta = array('Type' => $type);
			
			if($label !== NULL) {
				$msg_meta['Label'] = $label;
			}
			
			if(isset($meta['file'])) {
				$msg_meta['File'] = $meta['file'];
			}
			
			if(isset($meta['line'])) {
				$msg_meta['Line'] = $meta['line'];
			}
			
			$msg = '[' . $this->jsonEncode($msg_meta) . ',' . $this->jsonEncode($object) . ']';
		}
		
		$parts = explode("\n", chunk_split($msg, 5000, "\n"));
		
		for($i=0 ; $i<count($parts) ; $i++) {
			$part = $parts[$i];
			
			if($part) {
				if(count($parts) > 2) {
					// Message needs to be split into multiple parts
					$this->setHeader('X-Wf-1-' . $structure_index . '-' . '1-' . $this->_messageIndex,
						(($i==0)?strlen($msg):'') . '|' . $part . '|' . (($i < count($parts)-2) ? '\\' : ''));
				}
				else {
					$this->setHeader('X-Wf-1-'.$structure_index.'-'.'1-'.$this->_messageIndex,
						strlen($part) . '|' . $part . '|');
				}
				
				$this->_messageIndex++;
				
				if($this->_messageIndex > 99999) {
					throw new Exception('Maximum number (99,999) of messages reached!');
				}
			}
		}
		
		$this->setHeader('X-Wf-1-Index', $this->_messageIndex-1);
		
		return true;
	}
	
	/**
	 * Standardizes the specified path for Windows systems.
	 *
	 * @param string $path the path to use
	 * @return string the standardized path
	 */
	protected function standardizePath($path) {
		return preg_replace('/\\\\+/', '/', $path);
	}
	
	/**
	 * Escapes a string returned from a trace for Windows systems.
	 *
	 * @param string $trace the trace string to escape
	 * @return string the escaped string
	 */
	protected function escapeTrace($trace) {
		if(!$trace) return $trace;
		
		for($i=0 ; $i<sizeof($trace); $i++) {
			if(isset($trace[$i]['file'])) {
				$trace[$i]['file'] = $this->escapeTraceFile($trace[$i]['file']);
			}
		}
		
		return $trace;
	}
	
	/**
	 * Escapes the file string returned from a trace for Windows systems.
	 *
	 * @param string $file the file string to escape
	 * @return string the escaped string
	 */
	protected function escapeTraceFile($file) {
		// Check for Windows file path.
		if(strpos($file, '\\')) {
			// Strip to single slash.
			$file = preg_replace('/\\\\+/', '\\', $file);
			
			return $file;
		}
		
		return $file;
	}
	
	/**
	 * Sets a header response value to the specified name/value pair.
	 *
	 * @param string $name the name of the header value to set
	 * @param string $value the value to apply
	 */
	protected function setHeader($name, $value) {
		header($name . ': ' . $value);
	}
	
	/**
	 * Retrieves the client's user agent string.
	 *
	 * @return string the client's user agent
	 */
	protected function getUserAgent() {
		if(!isset($_SERVER['HTTP_USER_AGENT'])) return false;
		return $_SERVER['HTTP_USER_AGENT'];
	}
	
	/**
	 * Encodes an object into a JSON string
	 *
	 * @param object $object the object to be encoded
	 * @return string the encoded JSON string
	 */
	protected function jsonEncode($object) {
		if(function_exists('json_encode') && $this->_options['useNativeJsonEncode'] != false) {
			return json_encode($this->encodeObject($object));
    	}
    	else {
    		return $this->json_encode($this->encodeObject($object));
    	}
	}
	
	/**
	 * Encodes an object including members with protected and private
	 * visibility.
	 *
	 * @param object $object the object to be encoded
	 * @param int $depth the current traversal depth
	 * @return array all members of the object
	 */
	protected function encodeObject($object, $depth = 1) {
		$return = array();
		
		if(is_object($object)) {
			if($depth > $this->_options['maxObjectDepth']) {
				return '** Max Object Depth (' . $this->_options['maxObjectDepth'] . ') **';
			}
			
			foreach($this->_objectStack as $refVal) {
				if($refVal === $object) {
					return '** Recursion ('.get_class($object).') **';
				}
			}
			
			array_push($this->_objectStack, $object);
			$return['__className'] = $class = get_class($object);
			
			$reflectionClass = new \ReflectionClass($class);
			$properties = array();
			
			foreach($reflectionClass->getProperties() as $property) {
				$properties[$property->getName()] = $property;
			}
			
			$members = (array) $object;
			
			foreach($properties as $raw_name => $property) {
				$name = $raw_name;
				
				if($property->isStatic()) {
					$name = 'static:' . $name;
				}
				
				if($property->isPublic()) {
					$name = 'public:' . $name;
				}
				elseif($property->isPrivate()) {
					$name = 'private:' . $name;
					$raw_name = "\0" . $class . "\0" . $raw_name;
				}
				elseif($property->isProtected()) {
					$name = 'protected:' . $name;
					$raw_name = "\0" . '*' . "\0" . $raw_name;
				}
				
				if(array_key_exists($raw_name, $members) && !$property->isStatic()) {
					$return[$name] = $this->encodeObject($members[$raw_name], $depth + 1);
				}
				else {
					if(method_exists($property, 'setAccessible')) {
						$property->setAccessible(true);
						$return[$name] = $this->encodeObject($property->getValue($object), $depth + 1);
					}
					elseif($property->isPublic()) {
						$return[$name] = $this->encodeObject($property->getValue($object), $depth + 1);
					}
					else {
						$return[$name] = '** Need PHP 5.3 to get value **';
					}
				}
			}
			
			// Include all members that are not defined in the class but exist in the object.
			foreach($members as $name => $value) {
				if($name{0} == "\0") {
					$parts = explode("\0", $name);
					$name = $parts[2];
				}
				
				if(!isset($properties[$name])) {
					$name = 'undeclared:' . $name;
					$return[$name] = $this->encodeObject($value, $depth + 1);
				}
			}
			
			array_pop($this->_objectStack);
		}
		elseif(is_array($object)) {
			if($depth > $this->_options['maxArrayDepth']) {
				return '** Max Array Depth ('.$this->_options['maxArrayDepth'].') **';
			}
			
			foreach($object as $key => $val) {
				/**
				 * Encoding the $GLOBALS PHP array causes an infinite loop if
				 * the recursion is not reset here as it contains a reference to
				 * itself. This will avoid an infinite loop.
				 */
				if($key=='GLOBALS' && is_array($val) && array_key_exists('GLOBALS', $val)) {
					$val['GLOBALS'] = '** Recursion (GLOBALS) **';
				}
				
				$return[$key] = $this->encodeObject($val, $depth + 1);
			}
		}
		elseif(self::is_utf8($object)) {
			return $object;
		}
		else {
			return utf8_encode($object);
		}
		
		return $return;
	}
	
	/**
	 * Determines if the specified string has a valid UTF-8 encoding.
	 *
	 * @param string $string the string to check
	 * @return boolean the result of the test
	 */
	protected static function is_utf8($string) {
		return preg_match('%^(?:
			[\x09\x0A\x0D\x20-\x7E]              # ASCII
			| [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
			|  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
			| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
			|  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
			|  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
			| [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
			|  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
			)*$%xs', $string);
	}
	
	/**
	 * Converts a string from UTF-8 to UTF-16. Ths is normally handled by
	 * mb_convert_encoding, but will provide a slower PHP-only method for
	 * installations that lack the multibyte string extension.
	 *
	 * @param string $utf8 the UTF-8 string to convert
	 * @return string the converted string
	 */
	private function json_utf82utf16($utf8) {
		if(function_exists('mb_convert_encoding')) {
			return mb_convert_encoding($utf8, 'UTF-16', 'UTF-8');
		}
		
		switch(strlen($utf8)) {
			case 1:
				// This case should never be reached, because we are in ASCII range.
				return $utf8;
				break;
				
			case 2:
				// Return a UTF-16 character from a 2-byte UTF-8 char.
				return chr(0x07 & (ord($utf8{0}) >> 2)) . chr((0xC0 & (ord($utf8{0}) << 6)) | (0x3F & ord($utf8{1})));
				break;
				
			case 3:
				// Return a UTF-16 character from a 3-byte UTF-8 char.
				return chr((0xF0 & (ord($utf8{0}) << 4)) | (0x0F & (ord($utf8{1}) >> 2))) .
						chr((0xC0 & (ord($utf8{1}) << 6)) | (0x7F & ord($utf8{2})));
      }
      
      return '';
	}
	
	/**
	 * Formats the specified variable as JSON.
	 *
	 * @param mixed $var any number, boolean, string, array, or object to be encoded
	 * @return string the resulting JSON string
	 */
	private function json_encode($var) {
		if(is_object($var)) {
			if(in_array($var,$this->_json_objectStack)) {
				return '"** Recursion **"';
			}
		}
		
		switch(gettype($var)) {
			case 'boolean':
				return $var ? 'true' : 'false';
				break;
				
			case 'NULL':
				return 'null';
				break;
				
			case 'integer':
				return (int) $var;
				break;
				
			case 'double':
			case 'float':
				return (float) $var;
				break;
				
			case 'string':
				$ascii = '';
				$strlen_var = strlen($var);
				
				/**
				 * Iterate over every character in the string, escaping with a
				 * slash or encoding to UTF-8 where necessary.
				 */
				for($c = 0; $c < $strlen_var; ++$c) {
					$ord_var_c = ord($var{$c});
					
					switch (true) {
						case $ord_var_c == 0x08:
							$ascii .= '\b';
							break;
							
						case $ord_var_c == 0x09:
							$ascii .= '\t';
							break;
							
						case $ord_var_c == 0x0A:
							$ascii .= '\n';
							break;
							
						case $ord_var_c == 0x0C:
							$ascii .= '\f';
							break;
							
						case $ord_var_c == 0x0D:
							$ascii .= '\r';
							break;
							
						case $ord_var_c == 0x22:
						case $ord_var_c == 0x2F:
						case $ord_var_c == 0x5C:
							$ascii .= '\\'.$var{$c};
							break;
							
						case (($ord_var_c >= 0x20) && ($ord_var_c <= 0x7F)):
							// Characters U-00000000 - U-0000007F (same as ASCII)
							$ascii .= $var{$c};
							break;
							
						case (($ord_var_c & 0xE0) == 0xC0):
							// Characters U-00000080 - U-000007FF, mask 110XXXXX
							$char = pack('C*', $ord_var_c,
										ord($var{$c + 1}));
							
							$c += 1;
							$utf16 = $this->json_utf82utf16($char);
							$ascii .= sprintf('\u%04s', bin2hex($utf16));
							break;
							
						case (($ord_var_c & 0xF0) == 0xE0):
							// Characters U-00000800 - U-0000FFFF, mask 1110XXXX
							$char = pack('C*', $ord_var_c,
										ord($var{$c + 1}),
										ord($var{$c + 2}));
							
							$c += 2;
							$utf16 = $this->json_utf82utf16($char);
							$ascii .= sprintf('\u%04s', bin2hex($utf16));
							break;
							
						case (($ord_var_c & 0xF8) == 0xF0):
							// Characters U-00010000 - U-001FFFFF, mask 11110XXX
							$char = pack('C*', $ord_var_c,
										ord($var{$c + 1}),
										ord($var{$c + 2}),
										ord($var{$c + 3}));
							
							$c += 3;
							$utf16 = $this->json_utf82utf16($char);
							$ascii .= sprintf('\u%04s', bin2hex($utf16));
							break;
							
						case (($ord_var_c & 0xFC) == 0xF8):
							// Characters U-00200000 - U-03FFFFFF, mask 111110XX
							$char = pack('C*', $ord_var_c,
										ord($var{$c + 1}),
										ord($var{$c + 2}),
										ord($var{$c + 3}),
										ord($var{$c + 4}));
							
							$c += 4;
							$utf16 = $this->json_utf82utf16($char);
							$ascii .= sprintf('\u%04s', bin2hex($utf16));
							break;
							
						case (($ord_var_c & 0xFE) == 0xFC):
							// characters U-04000000 - U-7FFFFFFF, mask 1111110X
							$char = pack('C*', $ord_var_c,
										ord($var{$c + 1}),
										ord($var{$c + 2}),
										ord($var{$c + 3}),
										ord($var{$c + 4}),
										ord($var{$c + 5}));
							
							$c += 5;
							$utf16 = $this->json_utf82utf16($char);
							$ascii .= sprintf('\u%04s', bin2hex($utf16));
							break;
					}
				}
				
				return '"'.$ascii.'"';
				break;
				
			case 'array':
				/**
				 * As per JSON spec if any array key is not an integer we must
				 * treat the the whole array as an object. We also try to catch
				 * a sparsely populated associative array with numeric keys here
				 * because some JS engines will create an array with empty
				 * indexes up to max_index which can cause memory issues and
				 * because the keys, which may be relevant, will be remapped
				 * otherwise.
				 * 
				 * As per the ECMA and JSON specification an object may have any
				 * string as a property. Unfortunately due to a hole in the ECMA
				 * specification if the key is a ECMA reserved word or starts
				 * with a digit the parameter is only accessible using
				 * ECMAScript's bracket notation.
				 */
				
				// JSON Object
				if(is_array($var) && count($var) && (array_keys($var) !== range(0, sizeof($var) - 1))) {
					$this->_json_objectStack[] = $var;
					$properties = array_map(array($this, 'json_name_value'),
									array_keys($var),
									array_values($var));
					
					array_pop($this->_json_objectStack);
					
					foreach($properties as $property) {
						if($property instanceof Exception) {
							return $property;
						}
					}
					
					return '{' . join(',', $properties) . '}';
				}
				
				$this->_json_objectStack[] = $var;
				
				// Regular Array
				$elements = array_map(array($this, 'json_encode'), $var);
				
				array_pop($this->_json_objectStack);
				
				foreach($elements as $element) {
					if($element instanceof Exception) {
						return $element;
					}
				}
				
				return '[' . join(',', $elements) . ']';
				break;
				
			case 'object':
				$vars = get_object_vars($var);
				$this->_json_objectStack[] = $var;
				
				$properties = array_map(array($this, 'json_name_value'),
								array_keys($vars),
								array_values($vars));
				
				array_pop($this->_json_objectStack);
				
				foreach($properties as $property) {
					if($property instanceof Exception) {
						return $property;
					}
				}
				
				return '{' . join(',', $properties) . '}';
				
				break;
				
			default:
				return NULL;
		}
	}
	
	/**
	 * An array-walking function for use in generating JSON-formatted name-value
	 * pairs.
	 *
	 * @param string $name the name of the key to use
	 * @param mixed $value a reference to an array element to be encoded
	 * @return string the JSON-formatted name-value pair, like '"name":value'
	 */
	private function json_name_value($name, $value) {
		/**
		 * Encoding the $GLOBALS PHP array causes an infinite loop if the
		 * recursion is not reset here as it contains a reference to itself.
		 * This will avoid an infinite loop.
		 */
		if($name == 'GLOBALS' && is_array($value) && array_key_exists('GLOBALS', $value)) {
			$value['GLOBALS'] = '** Recursion **';
		}
		
		$encoded_value = $this->json_encode($value);
		
		if($encoded_value instanceof Exception) {
			return $encoded_value;
		}
		
		return $this->json_encode(strval($name)) . ':' . $encoded_value;
	}
}