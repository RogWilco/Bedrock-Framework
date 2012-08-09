<?php
namespace Bedrock\Common\Utils;

/**
 * General data manipulation utilities.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 11/10/2008
 * @updated 07/02/2012
 */
class Data extends \Bedrock {
    const HASH_SALT_LENGTH = 24;
    
    /**
     * Generates a hash from the specified string.
     *
     * @param string $string the string from which to generate a hash
     * @param string $salt the salt (if known)
     * @return string the generated hash
     */
    public static function hash($string, $salt = NULL) {
        try {
            // Setup
            $hashedText = '';
            
            if($string == '') {
                throw new \Bedrock\Common\Exception('The specified string was empty.');
            }
            
            // Get the specified salt string, or generate one if null.
            if($salt === NULL) {
                $salt = substr(hash('md5', uniqid(rand(), true)), 0, self::HASH_SALT_LENGTH);
            }
            else {
                $salt = substr($salt, 0, self::HASH_SALT_LENGTH);
            }
            
            // Create hash with salt.
            $hashedText = $salt.hash('sha1', $salt.$string);
            
            return $hashedText;
        }
        catch(\Exception $ex) {
            \Bedrock\Common\Logger::exception($ex);
        }
    }
    
    /**
     * Calculates the number of years since the specified date.
     * 
     * @param mixed $date the specified start date
     * @return integer the number of years
     */
    public static function yearsSince($date) {
        try {
            $date = is_string($date) ? strtotime($date) : $date;
            $secondsSince = time() - $date;
            $secondsInAYear = 31556926;
            $yearsSince = floor($secondsSince / $secondsInAYear);
            
            return $yearsSince;
        }
        catch(\Exception $ex) {
            \Bedrock\Common\Logger::exception($ex);
        }
    }
    
    /**
     * Returns a random string of characters of the specified length. Currently
     * the letter "l" and the number "1" have been left out because they can
     * easily be confused for each other.
     * 
     * @param integer $length the character length of the string to generate $length
     * @return string the generated string of random characters
     */
    public static function randString($length = 7) {
        try {
            $chars = 'abcdefghijkmnopqrstuvwxyz023456789';
            srand((double)microtime()*1000000);
            $i = 0;
			$result = '';
            
            while($i <= $length) {
                $num = rand()%33;
                $tmp = substr($chars, $num, 1);
				$result .= $tmp;
                $i++;
            }
            
            return $result;
        }
        catch(\Exception $ex) {
            \Bedrock\Common\Logger::exception($ex);
        }
    }
    
    /**
     * Rounds a number to the specified precision. Expands on PHP's round()
     * function to accomodate decimals correctly.
     * 
     * @param float $number the number to round
     * @param integer $precision the level of desired precision (default is 0)
     * @return float the rounded number
     */
    public function roundToFixed($number, $precision = 0) {
        try {
            $tempd = $number*pow(10,$precision);
            $tempd1 = round($tempd);
            $number = $tempd1/pow(10,$precision);
            
            return $number;
        }
        catch(\Exception $ex) {
            \Bedrock\Common\Logger::exception($ex);
        }
    }
    
    /**
     * Converts the specified value to the desired unit of measure (for data).
     * 
     * @param float $value the numeric value to convert
     * @param string $sourceUnits the numeric value's current units
     * @param string $destUnits the desired units to convert to
     * @return float the converted value
     */
    public function convertDataUnits($value, $sourceUnits, $destUnits) {
        try {
            $units['bytes'] = 0;
            $units['kilobytes'] = 1;
            $units['megabytes'] = 2;
            $units['gigabytes'] = 3;
            $units['terabytes'] = 4;
            $units['petabytes'] = 5;
            
            $exp = $units[$sourceUnits] - $units[$destUnits];
            
            $result = $value*pow(1024, $exp);
            
            return $result;
        }
        catch(\Exception $ex) {
            \Bedrock\Common\Logger::exception($ex);
        }
    }
}