<?php
namespace Bedrock\Common\Logger\Target;

/**
 * System Logger Target
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 08/07/2008
 * @updated 07/02/2012
 */
class System extends \Bedrock implements \Bedrock\Common\Logger\Target\TargetInterface {
	/**
	 * Default Constructor
	 *
	 * @param array $args the Growl initialization arguments
	 */
	public function __construct($args = array()) {
		parent::__construct();
		$this->open($args);
	}

	/**
	 * Opens a connection to the system logger.
	 *
	 * @param array $args the Growl initialization arguments
	 */
	public function open($args = array()) {
		openlog(\Bedrock\Common\Registry::get('config')->meta->title, LOG_PID | LOG_PERROR, LOG_USER);
	}

	/**
	 * Closes the current system logger connection.
	 */
	public function close() {
		closelog();
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
	 * @param array $data the data to write to the output stream
	 */
	public function write($data) {
		// Setup
		$level = 0;
		$message = '';

		switch($data[4]) {
			default:
			case \Bedrock\Common\Logger::TYPE_EVENT:
				$message = $data[2] . '::' . $data[3] . '() - ' . $data[0];
				break;

			case \Bedrock\Common\Logger::TYPE_TABLE:
				$message = $data[2] . '::' . $data[3] . '() - TABLE DATA';
				break;

			case \Bedrock\Common\Logger::TYPE_EXCEPTION:
				$message = $data[2] . '::' . $data[3] . '() - EXCEPTION: ' . $data[0];
				break;

			case \Bedrock\Common\Logger::TYPE_ENTRY:
				$message = $data[2] . '::' . $data[3] . '() - ENTRY';
				break;

			case \Bedrock\Common\Logger::TYPE_EXIT:
				$message = $data[2] . '::' . $data[3] . '() - EXIT';
				break;
		}

		switch($data[1]) {
			case 'TRAVERSE':
			case 'INFO':
				$level = LOG_NOTICE;
				break;

			case 'WARN':
				$level = LOG_WARNING;
				break;

			default:
			case 'ERROR':
				$level = LOG_ERR;
				break;
		}

		syslog($level, $message);
	}
}