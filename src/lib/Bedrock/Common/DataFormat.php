<?php
/**
 * Data Format Base Class
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 09/26/2008
 * @updated 09/26/2008
 */
abstract class Bedrock_Common_DataFormat extends Bedrock {
	const TYPE_XML = 0;
	const TYPE_YAML = 1;
	const TYPE_JSON = 3;
	const TYPE_CSV = 4;
	
	protected $_data;
	
	/**
	 * Initializes the DataFormat object.
	 */
	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * Outputs the data to the browser.
	 */
	public function printData() {
		Bedrock_Common_Logger::logEntry();
		
		try {
			switch(get_class($this)) {
				default:
					header('Content-Type: text/plain; charset=ISO-8859-1');
					break;
					
				case 'Bedrock_Common_DataFormat_XML':
					header('Content-Type: application/xml; charset=ISO-8859-1');
					echo '<?xml version="1.0" encoding="ISO-8859-1"?>' . Bedrock_Common::TXT_NEWLINE;
					break;
			}
			
			echo $this->toString();
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
	
	abstract function toArray();
	abstract function toString();
}
?>