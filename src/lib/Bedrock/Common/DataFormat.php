<?php
namespace Bedrock\Common;

/**
 * Data Format Base Class
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 09/26/2008
 * @updated 07/02/2012
 */
abstract class DataFormat extends \Bedrock {
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
		try {
			switch(get_class($this)) {
				default:
					header('Content-Type: text/plain; charset=ISO-8859-1');
					break;
					
				case 'Bedrock\\Common\\DataFormat\\XML':
					header('Content-Type: application/xml; charset=ISO-8859-1');
					echo '<?xml version="1.0" encoding="ISO-8859-1"?>' . \Bedrock\Common::TXT_NEWLINE;
					break;
			}
			
			echo $this->toString();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	abstract function toArray();
	abstract function toString();
}