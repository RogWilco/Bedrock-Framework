<?php
namespace Bedrock\Common\DataFormat;

/**
 * DataFormat Factory
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 09/26/2008
 * @updated 07/02/2012
 */
class Factory {
	/**
	 * Returns the correct DataFormat object of the requested type.
	 *
	 * @param mixed $format the desired format (either as a string or integer)
	 * @param array $data optional data to be included with the returned object
	 *
	 * @return \Bedrock\Common\DataFormat the corresponding DataFormat object
	 */
	public static function get($format, $data = array()) {
		switch(strtoupper($format)) {
			case 'XML':
			case \Bedrock\Common\DataFormat::TYPE_XML:
				return new \Bedrock\Common\DataFormat\XML($data);
				break;
				
			case 'YAML':
			case \Bedrock\Common\DataFormat::TYPE_YAML:
				return new \Bedrock\Common\DataFormat\YAML($data);
				break;
				
			case 'JSON':
			case \Bedrock\Common\DataFormat::TYPE_JSON:
				return new \Bedrock\Common\DataFormat\JSON($data);
				break;
				
			case 'CSV':
			case \Bedrock\Common\DataFormat::TYPE_CSV:
				return new \Bedrock\Common\DataFormat\CSV($data);
				break;
				
			default:
				return NULL;
		}
	}
}