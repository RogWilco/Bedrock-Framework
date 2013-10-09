<?php
namespace Bedrock\Control;

/**
 * Data Query API Controller
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 09/08/2008
 * @updated 07/02/2012
 */
class Query extends \Bedrock\Control {
	protected $_params;
	
	/**
	 * The main index for the controller.
	 *
	 * @param array $args an array of arguments passed from the GET string
	 */
	public function index($args) {
		try {
			// Setup
			$this->_params = $this->getParams($args);
			
			// Delegate to Action
			switch($this->_params['action']) {
				case 'get':
					$this->get();
					break;
					
				case 'set':
					$this->set();
					break;
					
				default:
					// Do Nothing
					break;
			}
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Builds a query and retrieves data corresponding to the specified URL
	 * parameters.
	 */
	public function get() {
		try {
			// Setup
			$results = array();

			// Query Database
			$query = \Bedrock\Model\Query::from($this->_params['table']);
			
			// Query: Where Clause
			if($this->_params['where']) {
				foreach($this->_params['where'] as $field => $where) {
					foreach($where as $operator => $value) {
						if($value) {
							$query = $query->where($field, $operator, $value);
						}
					}
				}
			}
			
			// Query: Sort Statement
			if($this->_params['sort'] && is_array($this->_params['sort'])) {
				foreach($this->_params['sort'] as $sort) {
					$query = $query->sort($sort[0] . ' ' . strtoupper($sort[1]));
				}
			}
			
			// Query: Limit Statement
			if($this->_params['limit']) {
				if(!$this->_params['page']) {
					$this->_params['page'] = 1;
				}
				
				$query = $query->limit($this->_params['page']*$this->_params['limit']-$this->_params['limit'], $this->_params['limit']);
			}
			
			$rows = $query->execute();
			
			// Format Rows for Data Format
			switch($this->_params['format']) {
				default:
				case 'xml':
					$format = \Bedrock\Common\DataFormat::TYPE_XML;
					$reorgRows = array();

					foreach($rows as $row) {
						$reorgRows[]['row'] = array($row);
					}
					
					$results[]['rows'] = $reorgRows;
					break;
					
				case 'yaml':
					$format = \Bedrock\Common\DataFormat::TYPE_YAML;
					foreach($rows as $row) {
						$results[]['row'] = array($row);
					}
					break;
					
				case 'flexigrid':
				case 'json':
					$format = \Bedrock\Common\DataFormat::TYPE_JSON;
					$results[]['page'] = $this->_params['page'];
					$results[]['total'] = $rows->countAll();
					$results[]['rows'] = $rows;
					break;
					
				case 'csv':
					$format = \Bedrock\Common\DataFormat::TYPE_CSV;
					break;
			}
			
			$response = \Bedrock\Common\DataFormat\Factory::get($format, $results);
			$response->printData();
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Builds a query and saves the data passed via URL parameters.
	 */
	public function set() {
		try {
			// Setup
			//$table = $args[0];
			// TODO: implement this method
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
	
	/**
	 * Returns the currently passed parameters for the particular query action
	 *
	 * @param array $args the supplied arguments
	 *
	 * @return array the corresponding parameters
	 */
	protected function getParams($args) {
		try {
			// Setup
			$params = array();
			
			foreach($args as $arg) {
				$arg = explode(':', $arg);
				
				// Parse Argument/Check Type
				switch($arg[0]) {
					default:
						$params[$arg[0]] = $arg[1];
						break;
						
					case 'query':
						if(!isset($arg[1])) {
							$arg[1] = 'flexigrid';
						}
						
						$params['format'] = $arg[1];
						break;
						
					case 'get':
					case 'set':
						$params['action'] = $arg[0];
						
						$tableFields = explode('|', $arg[1]);
						
						$params['table'] = $tableFields[0];
						
						if($tableFields[1]) {
							$params['fields'] = explode(',', $tableFields[1]);
						}
						break;
						
					case 'where':
						$whereParams = explode('|', $arg[1]);
						
						foreach($whereParams as $whereParam) {
							if(substr_count($whereParam, '=')) {
								$whereParamParts = explode('=', $whereParam);
								
								$params[$arg[0]][$whereParamParts[0]]['='] = $whereParamParts[1];
							}
							elseif(substr_count($whereParam, '<>')) {
								$whereParamParts = explode('<>', $whereParam);
								$params[$arg[0]][$whereParamParts[0]]['!'] = $whereParamParts[1];
							}
							elseif(substr_count($whereParam, '>')) {
								$whereParamParts = explode('>', $whereParam);
								
								$params[$arg[0]][$whereParamParts[0]]['>'] = $whereParamParts[1];
							}
							elseif(substr_count($whereParam, '<')) {
								$whereParamParts = explode('<', $whereParam);
								
								$params[$arg[0]][$whereParamParts[0]]['<'] = $whereParamParts[1];
							}
						}
						break;
						
					case 'sort':
						$sortParams = explode('|', $arg[1]);
						
						foreach($sortParams as $key => $sortParam) {
							$params[$arg[0]][] = explode(',', $sortParam);
						}
						break;
				}
			}
			
			if($params['format'] == 'flexigrid') {
				$params['page'] = $_POST['page'];
				$params['limit'] = $_POST['rp'];
				
				if(!is_array($params['sort'])) {
					$params['sort'][] = array($_POST['sortname'], $_POST['sortorder']);
				}
				else {
					array_unshift($params['sort'], array($_POST['sortname'], $_POST['sortorder']));
				}
			}
			return $params;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
		}
	}
}