<?php
/**
 * File Access Class
 * 
 * Provides basic file access functionality.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 03/27/2008
 * @updated 03/27/2008
 */
class Bedrock_Common_File extends Bedrock
{
	/**
	 * The path of the file.
	 * 
	 * @var string
	 */
	private $_path;
	
	/**
	 * The name of the file.
	 * 
	 * @var string
	 */
	private $_filename;
	
	/**
	 * The complete file path, including the file name.
	 * 
	 * @var string
	 */
	private $_filepath;
	
	/**
	 * The file handle used to access the file.
	 * 
	 * @var mixed
	 */
	private $_handle;
	
	/**
	 * Default Constructor
	 * 
	 * @param string $path the path of the file
	 * @param string $filename the name of the file
	 * @return void
	 */
	public function __construct($path, $filename) {
		$this->_path = $path;
		$this->_filename = $filename;
		$this->_filepath = $path.DIRECTORY_SEPARATOR.$filename;
		
		$this->_handle = fopen($this->_filepath, 'a+') or die("ERROR: Cannot open file ".$this->_filepath);
	}
	
	/**
	 * Magic function for serializing.
	 * 
	 * @return void
	 */
	public function __sleep() {
		$this->closeFile();
	}
	
	/**
	 * Magic function for underializing.
	 * 
	 * @return void
	 */
	public function __wakeup() {
		$this->_handle = fopen($this->_filepath, 'a+') or die("ERROR: Cannot open file ".$this->_filepath);
	}

	/**
	* Adds the specified string to a new line in the file.
	* 
	* @param string $string the string to append to the file
	* @return void
	*/
	public function addLine($string) {
		fwrite($this->_handle, $string."\r\n");
	}
	
	/**
	* Closes the file when all actions are complete.
	* 
	* @return void
	*/
	public function closeFile() {
		fclose($this->_handle);
	}
}
?>