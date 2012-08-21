<?php
namespace Bedrock\Common\Logger\Target;

/**
 * FirePHP Logger Target
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 6/12/2009
 * @updated 07/02/2012
 */
class FirePHP extends \Bedrock implements \Bedrock\Common\Logger\Target\TargetInterface {
	protected $_firePHP = NULL;

	/**
	 * Default constructor.
	 *
	 * @param array $args any initialization arguments
	 */
	public function __construct($args = array()) {
		parent::__construct();
		$this->open($args);
	}

	/**
	 * Opens a FirePHP output stream using the specified parameters.
	 *
	 * @see \Bedrock\Common\Logger\Target\TargetInterface::open()
	 * @param array $args any initialization arguments
	 */
	public function open($args = array()) {
		// Initialize FirePHP Connection
		$this->_firePHP = \Bedrock\Common\FirePHP::getInstance(true);

		// Start Output-Buffering
		ob_start();
	}

	/**
	 * Closes the current FirePHP stream.
	 *
	 * @see \Bedrock\Common\Logger\Target\TargetInterface::close()
	 */
	public function close() {
		$this->_firePHP = NULL;
	}

	/**
	 * Returns the format accepted by this Target.
	 *
	 * @return string the accepted input format
	 */
	public function getFormat() {
		return 'array';
	}

	/**
	 * Writes data to the output stream.
	 *
	 * @see \Bedrock\Common\Logger\Target\TargetInterface::write()
	 * @param array $data the data to write to the output stream
	 */
	public function write($data) {
		// Setup
		$message = $data[0];
		$type = $data[1];
		$class = $data[2];
		$function = $data[3];
		$table = $data[4] == \Bedrock\Common\Logger::TYPE_TABLE ? true : false;
		$exception = false;
		$label = $class . '::' . $function . '()';

		if(!$table) {
			$exception = $data[4] == \Bedrock\Common\Logger::TYPE_EXCEPTION ? true : false;
		}

		// Determine Type
		switch($type) {
			case 'TRAVERSE':
				if($message == '__ENTRY__') {
					$type = \Bedrock\Common\FirePHP::TYPE_GROUP_START;
				}
				elseif($message == '__EXIT__') {
					$type = \Bedrock\Common\FirePHP::TYPE_GROUP_END;
				}
				else {
					throw new \Bedrock\Common\Logger\Target\Exception('Invalid TRAVERSE data type passed.');
				}
				break;

			default:
			case 'INFO':
				if($table) {
					$type = \Bedrock\Common\FirePHP::TYPE_TABLE;
				}
				else {
					$type = \Bedrock\Common\FirePHP::TYPE_INFO;
				}
				break;

			case 'WARN':
				$type = \Bedrock\Common\FirePHP::TYPE_WARN;
				break;

			case 'ERROR':
				if($exception) {
					$type = \Bedrock\Common\FirePHP::TYPE_EXCEPTION;
				}
				else {
					$type = \Bedrock\Common\FirePHP::TYPE_ERROR;
				}
				break;
		}

		// Send Message
		if(isset($this->_firePHP)) {
			if($type == \Bedrock\Common\FirePHP::TYPE_GROUP_START || $type == \Bedrock\Common\FirePHP::TYPE_GROUP_END) {
				if($class != 'global' && $function != 'main') {
					$this->_firePHP->fb($message, $label, $type);
				}
			}
			elseif($type == \Bedrock\Common\FirePHP::TYPE_TABLE) {
				$this->_firePHP->fb($message[1], $message[0], $type);
			}
			elseif($type == \Bedrock\Common\FirePHP::TYPE_EXCEPTION) {
				$this->_firePHP->fb($message);
			}
			else {
				$this->_firePHP->fb($message, $type);
			}
		}
		else {
			throw new \Bedrock\Common\Logger\Target\Exception('No FirePHP stream has been initialized.');
		}
	}
}