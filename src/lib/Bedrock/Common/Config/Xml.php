<?php
namespace Bedrock\Common\Config;

/**
 * XML Configuration
 *
 * A configuration object that can be built from an XML configuration file.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 03/29/2008
 * @updated 07/02/2012
 */
class Xml extends \Bedrock\Common\Config {
	/**
	 * Initializes an XML-based Config object.
	 *
	 * @param string $file the XML file to store/load configuration data
	 * @param mixed $section the name of the current section
	 * @param boolean $locked whether or not the data is writeable
	 */
	public function __construct($file, $section = null, $locked = true) {
		// Setup
		$data = array();
		
		// Load XML File
		if(empty($file)) {
			throw new \Bedrock\Common\Config\Exception('No file specified.');
		}

		$config = simplexml_load_file($file);
		
		// Store Data
		if($section === null) {
			foreach($config as $key => $value) {
				$data[$key] = $this->_extends($config, $key);
			}
		}
		elseif(is_array($section)) {
			foreach($section as $aSection) {
				if(!$config->$aSection) {
					throw new \Bedrock\Common\Config\Exception('The section "' . $aSection . '" could not be found.');
				}

				$data = array_merge($this->_extends($config, $aSection), $data);
			}
		}
		else {
			if(!$config->$section) {
				throw new \Bedrock\Common\Config\Exception('The section "' . $section . '" could not be found.');
			}

			$data = $this->_extends($config, $section);

			if(!is_array($data)) {
				$data = array($section => $data);
			}
		}
		
		parent::__construct($data, $locked);
		$this->_section = $section;
	}

	/**
	 * Converts a SimpleXMLElement object into an array recurrsively.
	 *
	 * @param \SimpleXMLElement $xmlObject the object to convert
	 * @return array the converted object
	 */
	protected function _xmlToArray($xmlObject) {
		// Setup
		$result = array();

		if(count($xmlObject->children())) {
			foreach($xmlObject->children() as $key => $value) {
				if($value->children()) {
					$value = $this->_xmlToArray($value);
				}
				else {
					$value = (string) $value;
				}

				if(array_key_exists($key, $result)) {
					if(!is_array($result[$key]) || !array_key_exists(0, $result[$key])) {
						$result[$key] = array($result[$key]);
					}

					$result[$key][] = $value;
				}
				else {
					$result[$key] = $value;
				}
			}
		}
		elseif(!isset($xmlObject['extends'])) {
			$result = (string) $xmlObject;
		}
		
		return $result;
	}

	/**
	 * Applies an extended section to the specified configuration.
	 *
	 * @param \SimpleXMLElement $xmlObject the SimpleXMLElement containing the extending section
	 * @param string $section the name of the section
	 * @param array $config the configuration to extend
	 *
	 * @throws \Bedrock\Common\Config\Exception if the specified section cannot be found
	 * @return array the extended configuration
	 */
	protected function _extends($xmlObject, $section, $config = array()) {
		// Check for Section
		if(!$xmlObject->$section) {
			throw new \Bedrock\Common\Config\Exception('Section "' . $section . '" was not found.');
		}

		$thisSection = $xmlObject->$section;

		if(isset($thisSection['extends'])) {
			$config = $this->_extends($xmlObject, $thisSection['extends'], $config);
		}

		$config = $this->_mergeArrays($config, $this->_xmlToArray($thisSection));
		
		return $config;
	}

	/**
	 * Merges two arrays, replacing values from the first with values from the
	 * second for matched keys.
	 *
	 * @param array $arrayOne the primary array to merge
	 * @param array $arrayTwo the additional array to merge
	 * @return array the merged array
	 */
	protected function _mergeArrays($arrayOne, $arrayTwo) {
		foreach($arrayTwo as $key => $value) {
			if(is_array($value)) {
				if(!array_key_exists($key, $arrayOne)) {
					$arrayOne[$key] = array();
				}

				$arrayOne[$key] = $this->_mergeArrays($arrayOne[$key], $value);
			}
			else {
				$arrayOne[$key] = $value;
			}
		}

		return $arrayOne;
	}
}