<?php
namespace Bedrock\Common;

/**
 * Provides access to useful command line functionality.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 09/07/2020
 * @updated 09/07/2020
 */
class CLI {
	const COLOR_BLACK = "\033[0;30m";
	const COLOR_RED = "\033[0;31m";
	const COLOR_GREEN = "\033[0;32m";
	const COLOR_YELLOW = "\033[0;33m";
	const COLOR_BLUE = "\033[0;34m";
	const COLOR_MAGENTA = "\033[0;35m";
	const COLOR_CYAN = "\033[0;36m";
	const COLOR_WHITE = "\033[0;37m";
	const DIV_H1 = '================================================================================';
	const DIV_H2 = '--------------------------------------------------------------------------------';

	/**
	 * Attempts to retrieve the specified command line argument, using the
	 * specified default value if not found (null by default).
	 *
	 * @param integer $index the index of the argument to be retrieved
	 * @param null $default the default value to be used
	 *
	 * @return mixed the corresponding value
	 */
	public static function getArg($index, $default = null) {
		// Setup
		global $argv;

		if(array_key_exists($index, $argv)) {
			return $argv[$index];
		}
		else {
			return $default;
		}
	}

	public static function getArgString() {

	}

	/**
	 * Applies the specified prefix string to the beginning of every new line
	 * inside the specified text.
	 *
	 * @param string $text the text to be modified
	 * @param string $linePrefix the string to be prefixed to each line
	 *
	 * @return string the modified text
	 */
	public static function prefixLines($text = '', $linePrefix = '') {
		return $linePrefix . preg_replace('/\n/', "\n" . $linePrefix, $text);
	}
	
	/**
	 * Displays the specified text, optionally using the specified terminal color.
	 *
	 * @param string $text the text to be displayed
	 * @param string $color the desired color to be used
	 *
	 * @return void
	 */
	public static function printText($text, $color = self::COLOR_WHITE) {
		echo $color . $text;
	}

	/**
	 * Displays the specified text as a separate line of output, optionally
	 * using the specified terminal color.
	 *
	 * @param string $text the text to be displayed
	 * @param string $color the desired color to be used
	 *
	 * @return void
	 */
	public static function printLine($text = '', $color = self::COLOR_WHITE) {
		self::printText($text . "\n", $color);
	}

	/**
	 * Outputs a title block useful for titles, application version info, etc.
	 *
	 * @param string $text the text to be included within the title block
	 * @param string $linePrefix an optional string to be prefixed to every new line inside the specified text
	 * @param string $color the desired color to be used
	 *
	 * @return void
	 */
	public static function printTitle($text = '', $linePrefix = '', $color = self::COLOR_RED) {
		self::printLine(self::DIV_H1, self::COLOR_BLACK);
		self::printLine(self::prefixLines($text, $linePrefix), $color);
		self::printLine(self::DIV_H1, self::COLOR_BLACK);
	}
	
	/**
	 * Outputs the starting header for a section.
	 *
	 * @param string $text the text to be included within the header block
	 * @param string $linePrefix an optional string to be prefixed to every new line inside the specified text
	 * @param string $color the desired color to be used
	 *
	 * @return void
	 */
	public static function printSectionStart($text = '', $linePrefix = '', $color = self::COLOR_YELLOW) {
		self::printLine(self::prefixLines($text, $linePrefix), $color);
		self::printLine(self::DIV_H2, self::COLOR_BLACK);
		self::printLine();
	}
	
	/**
	 * Outputs the ending divider for a section.
	 *
	 * @return void
	 */
	public static function printSectionEnd() {
		self::printLine();
		self::printLine(self::DIV_H2, self::COLOR_BLACK);
		self::printLine();
	}

	public static function printTabbedLayout($data) {
		// Setup
		$columnSizes = array();

		if(is_array($data) && count($data) && is_array($data[0])) {
			foreach($data as $row => $fields) {
				foreach($fields as $col => $field) {
					if(!array_key_exists($col, $columnSizes)) {
						$columnSizes[$col] = 0;
					}

					$length = strlen($field);

					if($length > $columnSizes[$col]) {
						$columnSizes[$col] = $length;
					}
				}
			}

			foreach($data as $row => $fields) {
				$out = '';

				foreach($fields as $col => $field) {
					$out .= str_pad($field, ($columnSizes[$col] + 1), ' ', STR_PAD_RIGHT) . ' ';
				}

				self::printLine($out);
			}
		}
	}
}
