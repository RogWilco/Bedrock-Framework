<?php
/**
 * General utilities related to the model layer.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 10/13/2008
 * @updated 10/13/2008
 */
class Bedrock_Model_Utilities extends Bedrock_Model {
	/**
	 * Attempts to retrieve a mapping table name for the two specified tables.
	 * If the two tables do not exist, or no association is defined, an error
	 * will be thrown.
	 *
	 * @param string $firstTableName the name of the first associated table
	 * @param string $secondTableName the name of the second associated table
	 * @return string the name of the mapping table
	 */
	public static function getMappingTableName($firstTableName, $secondTableName) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = '';
			$connection = Bedrock_Common_Registry::get('database')->getConnection();
			
			// Query for Mapping Table
			$sql = 'SHOW TABLE STATUS WHERE Comment LIKE \'map|%\' AND (INSTR(Name, \'' . self::sanitize($firstTableName) . '\') > 0 OR INSTR(Name, \'' . self::sanitize($secondTableName) . '\') > 0)';
			Bedrock_Common_Logger::info('Querying for mapping table: "' . $sql . '"');
			$res = $connection->query($sql)->fetch(PDO::FETCH_ASSOC);
			
			if(!$res) {
				throw new Bedrock_Model_Exception('A mapping table for the specified tables could not be found.');
			}
			
			$result = $res['Name'];
			Bedrock_Common_Logger::info('Mapping table found: "' . $result . '"');
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(PDOException $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Exception('A database error was encountered while attempting to retrieve a mapping table name for "' . $firstTableName . '" and "' . $secondTableName . '"');
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Model_Exception('A problem was encountered while attempting to retrieve a mapping table name for "' . $firstTableName . '" and "' . $secondTableName . '"');
		}
	}
	
	/**
	 * Retrieves the names for all tables associated with the specified table.
	 *
	 * @param string $tableName the table to find associations with
	 * @return array an array of associated tables and the type of association
	 */
	public static function getAssociatedTableNames($tableName) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$result = array();
			$connection = Bedrock_Common_Registry::get('database')->getConnection();
			
			// Query for Associations
			$sql = 'SHOW TABLE STATUS WHERE Comment LIKE \'table|%\' AND ' .
					'(Comment LIKE \'%:' . self::sanitize($tableName) . '(%\' OR ' .
					'Comment LIKE \'%,' . self::sanitize($tableName) . '(%\')';
			
			Bedrock_Common_Logger::info('Querying for associated tables: "' . $sql . '"');
			
			$res = $connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			
			foreach($res as $row) {
				// Parse Association Type
				$mappings = explode(',', substr($row['Comment'], 15));
				
				foreach($mappings as $mapping) {
					if(substr($mapping, 0, strpos($mapping, '(')) == $tableName) {
						$matches = array();
						preg_match('#\((.*?)\)#', $mapping, $matches);
						$type = $matches[1];
					}
				}
				
				$result[$row['Name']] = $type;
			}
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
		}
	}
}
?>