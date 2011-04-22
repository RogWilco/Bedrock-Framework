<?php
/**
 * DataFormat Factory
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 09/26/2008
 * @updated 09/26/2008
 */
class Bedrock_Common_DataFormat_Factory {
	/**
	 * Returns the correct DataFormat object of the requested type.
	 *
	 * @param mixed $format the desired format (either as a string or integer)
	 */
	public static function get($format, $data = array()) {
		switch(strtoupper($format)) {
			case 'XML':
			case Bedrock_Common_DataFormat::TYPE_XML:
				return new Bedrock_Common_DataFormat_XML($data);
				break;
				
			case 'YAML':
			case Bedrock_Common_DataFormat::TYPE_YAML:
				return new Bedrock_Common_DataFormat_YAML($data);
				break;
				
			case 'JSON':
			case Bedrock_Common_DataFormat::TYPE_JSON:
				return new Bedrock_Common_DataFormat_JSON($data);
				break;
				
			case 'CSV':
			case Bedrock_Common_DataFormat::TYPE_CSV:
				return new Bedrock_Common_DataFormat_CSV($data);
				break;
				
			default:
				return NULL;
		}
	}
}
?>