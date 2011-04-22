<?php
/**
 * Base model object, loosely applying the active record pattern.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 07/09/2008
 * @updated 07/09/2008
 */
abstract class Bedrock_Model extends Bedrock {
	const FORMAT_SQL = 'sql';
	const FORMAT_CSV = 'csv';
	const FORMAT_XML = 'xml';
	const FORMAT_YAML = 'yaml';

	protected $_database;
	protected $_connection;
	
	/**
	 * Initializes the object.
	 * 
	 * @param $database the database object to use
	 */
	public function __construct($database = null) {
		if($database) {
			$this->_database = $database;
		}
		else {
			$this->_database = Bedrock_Common_Registry::get('database');
		}
		
		$this->_connection = $this->_database->getConnection();
		
		parent::__construct();
	}

		/**
	 * Sanitizes the specified value for safe insertion into the database.
	 *
	 * @param mixed $value the value to be sanitized
	 * @return mixed the sanitized value
	 */
	public static function sanitize($value) {
		Bedrock_Common_Logger::logEntry();

		try {
			Bedrock_Common_Logger::logExit();
			return addslashes($value);
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Exception('There was a problem sanitizing the specified data.');
		}
	}

	/**
	 * Reverts a sanitized value after being retrieved from the database.
	 *
	 * @param mixed $value the value being desanitized
	 * @return mixed the desanitized value
	 */
	public static function desanitize($value) {
		Bedrock_Common_Logger::logEntry();

		try {
			Bedrock_Common_Logger::logExit();
			return stripslashes($value);
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Exception('There was a problem sanitizing the specified data.');
		}
	}

	/**
	 * Writes the specified data to the specified location.
	 *
	 * @param string $fileLocation the location to write to
	 * @param string $fileContents the data to write to the file
	 */
	public static function writeFile($fileLocation, $fileContents) {
		if(is_file($fileLocation)) {
			unlink($fileLocation);
		}

		$directory = substr($fileLocation, 0, strrpos($fileLocation, DIRECTORY_SEPARATOR));

		if(!is_dir($directory)) mkdir($directory, 0777, true);

		$fileHandle = fopen($fileLocation, 'w+');
		fwrite($fileHandle, $fileContents);
		fclose($fileHandle);
	}
}
?>