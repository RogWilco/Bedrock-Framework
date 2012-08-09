<?php
namespace Bedrock\Common;

/**
 * Provides basic image manipulation for common image formats.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 2009-07-21
 * @updated 07/02/2012
 */
class Image extends \Bedrock {
	const TINT_BLACKANDWHITE = 0;

	protected static $_methods = null;
	protected $_location;
	protected $_properties = array();
	protected $_changed = array();

	/**
	 * Initializes a new image object.
	 *
	 * @param string $location the location of the image file
	 */
	public function __construct($location) {
		try {
			\Bedrock\Common\Logger::logEntry();

			if(!is_file($location)) {
				\Bedrock\Common\Logger::logExit();
				throw new \Bedrock\Common\Image\Exception('No image file was found at the location "' . $location . '"');
			}

			$this->_location = $location;
			$this->load();

			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Image\Exception('An image could not be initialized.');
		}
	}

	/**
	 * Retrieves the requested property's value.
	 *
	 * @param string $name the name of the property
	 * @return mixed the corresponding property's value
	 */
	public function __get($name) {
		if($this->_changed[$name]) {
			return $this->_changed[$name];
		}
		else {
			return $this->_properties[$name];
		}
	}

	/**
	 * Sets the specified property on the current image.
	 *
	 * @param string $name the name of the property to set
	 * @param mixed $value the value to apply
	 */
	public function __set($name, $value) {
		$this->_changed[$name] = $value;
	}

	/**
	 * Loads the current image file and all its properties.
	 */
	public function load() {
		try {
			\Bedrock\Common\Logger::logEntry();

			// Reset Changed Properties
			$this->_changed = array();

			// Load: Dimensions
			$command = self::$_methods['identify'] . ' -format "%w %h %m %f %d" ' . $this->_location;
			$result = exec($command);

			\Bedrock\Common\Logger::info('Retrieved properties for image: ' . $result);
			$result = explode(' ', $result);

			$this->_properties['width'] = $result[0];
			$this->_properties['height'] = $result[1];
			$this->_properties['type'] = $result[2];
			$this->_properties['filename'] = $result[3];
			$this->_properties['directory'] = $result[4];

			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Image\Exception('The image could not be loaded.');
		}
	}

	/**
	 * Commits any altered properties to the image file.
	 */
	public function commit() {
		try {
			\Bedrock\Common\Logger::logEntry();

			if(count($this->_changed) > 0) {
				// Commit: Size
				if($this->_changed['width'] || $this->_changed['height']) {
					// Determine new dimensions.
					$newWidth = $this->_changed['width'] ? $this->_changed['width'] : $this->_properties['height'];
					$newHeight = $this->_changed['height'] ? $this->_changed['height'] : $this->_properties['height'];

					// Apply Changes
					\Bedrock\Common\Logger::info('Adjusting image dimensions to: ' . $newWidth . 'x' . $newHeight);
					

					// Update Properties
					$this->_properties['width'] = $newWidth;
					$this->_properties['height'] = $newHeight;
				}

				// Commit: Image Type
				if($this->_changed['type']) {

					// Apply Changes
					\Bedrock\Common\Logger::info('Adjusting image type: ' . $this->_changed['type']);
					

					// Update Properties
					$this->_properties['type'];
				}

				// Commit: Filename
				if($this->_changed['filename'] || $this->_changed['directory']) {
					// Determine new filename/directory.
					$newFilename = $this->_changed['filename'] ? $this->_changed['filename'] : $this->_properties['filename'];
					$newDirectory = $this->_changed['directory'] ? $this->_changed['directory'] : $this->_properties['directory'];

					// Apply Changes
					\Bedrock\Common\Logger::info('Adjusting image filename/directory: ' . $newDirectory . $newFilename);
					

					// Update Properties
					$this->_properties['filename'] = $newFilename;
					$this->_properties['directory'] = $newDirectory;
					$this->_location = $this->_properties['directory'] . $this->_properties['filename'];
				}
			}

			// Reset
			$this->_changed = array();

			\Bedrock\Common\Logger::logExit();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Image\Exception('The image could not be loaded.');
		}
	}

	/**
	 * Executes the specified method with the specified parameters.
	 * 
	 * @param string $method a valid ImageMagick command-line utility
	 * @param array $params an array of key-value pairs to use as parameters
	 */
	public static function exec($method, $params) {
		try {
			\Bedrock\Common\Logger::logEntry();

			// Setup
			$command = '';

			if(!self::$_methods[$method]) {
				\Bedrock\Common\Logger::logExit();
				throw new \Bedrock\Common\Image\Exception('Unknown method "' . $method . '" specified.');
			}

			// Load Paths
			if(self::$_methods === null) {
				$root = \Bedrock\Common\Registry::get('config')->root->image;
				self::$_methods = array(
					'animate' => $root . 'animate',
					'compare' => $root . 'compare',
					'composite' => $root . 'composite',
					'conjure' => $root . 'conjure',
					'convert' => $root . 'convert',
					'display' => $root . 'display',
					'identify' => $root . 'identify',
					'import' => $root . 'import',
					'mogrify' => $root . 'mogrify',
					'montage' => $root . 'montage',
					'stream' => $root . 'stream'
				);
			}

			// Build Command
			$command = self::$_methods[$method];

			foreach($params as $key => $value) {
				if(is_numeric($key)) {
					$command .= ' ' . $value;
				}
				else {
					$command .= ' -' . $key . ' ' . $value;
				}
			}

			$command = escapeshellcmd($command);

			\Bedrock\Common\Logger::info('Executing command: ' . $command);

			exec($command);
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Image\Exception('The method "' . $method . '" could not be executed.');
		}
	}

	public static function resize($image, $width, $height) {
		
	}

	public static function crop($image, $width, $height) {

	}

	public static function monochrome($image, $tint = self::TINT_BLACKANDWHITE) {

	}
}