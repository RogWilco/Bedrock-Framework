<?php
namespace Bedrock\Common\Logger\Target;

/**
 * Provides a basic interface that, when implemented, allows an object to be
 * used as a target by the Logger.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 6/12/2009
 * @updated 07/02/2012
 */
interface TargetInterface {
	public function open($args = array());
	public function close();
	public function getFormat();
	public function write($data);
}