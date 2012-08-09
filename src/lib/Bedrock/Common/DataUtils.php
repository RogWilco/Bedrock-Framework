<?php
namespace Bedrock\Common;

/**
 * Data Utilities Class
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 10/02/2008
 * @updated 07/02/2012
 */
class DataUtils extends \Bedrock {
	/**
	 * Determines whether or not the specified array is associative or
	 * sequential.
	 *
	 * @param array $array the array to check
	 * @return boolean whether or not the array is associative
	 */
	public static function isAssoc($array) {
		return array_keys($array) != range(0, count($array) - 1);
	}
}