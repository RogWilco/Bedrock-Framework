<?php
namespace Bedrock\Common\Auth\Protocol;

/**
 * An Auth protocol allowing for authentication via Bedrock's Model layer.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 05/08/2009
 * @updated 07/02/2012
 */
class Model extends \Bedrock implements \Bedrock\Common\Auth\Protocol\ProtocolInterface {
	protected $_id = null;
	protected $_username = null;
	protected $_password = null;
	protected $_response = \Bedrock\Common\Auth::RESULT_FAILED;
	protected $_failCount = 0;
	protected $_table = null;
	protected $_fieldUser = null;
	protected $_fieldPassword = null;

	/**
	 * Initializes a new authentication protocol with the specified username and
	 * password.
	 *
	 * @param string $username a valid username
	 * @param string $password the corresponding password
	 * @param string $table the database table to use
	 * @param string $fieldUser the field used to store usernames
	 * @param string $fieldPassword the field used to store passwords
	 */
	public function __construct($username, $password, $table = 'users', $fieldUser = 'username', $fieldPassword = 'password') {
		try {
			$this->_username = $username;
			$this->_password = $password;
			$this->_table = $table;
			$this->_fieldUsername = $fieldUser;
			$this->_fieldPassword = $fieldPassword;

		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Auth\Exception('The Model authentication protocol could not be initialized.');
		}
	}

	/**
	 * Attempts to authenticate the specified user with the specified password.
	 * Returns the result of the authentication process (either the user's
	 * unique ID, or a failure code on failure).
	 *
	 * @return mixed the result of the authentication process, on success the user's unique ID
	 */
	public function authenticate() {
		try {
			// Query Database
			$db = \Bedrock\Common\Registry::get('database')->getConnection();
			$statement = $db->prepare('SELECT ' . $this->_fieldUsername . ', ' . $this->_fieldPassword . ' FROM ' . $this->_table . ' WHERE ' . $this->_fieldUsername . '=:username');
			$statement->execute(array('username' => $this->_username));
			$res = $statement->fetchAll();

			if(count($res) == 0) {
				$this->_id = \Bedrock\Common\Auth::RESULT_FAILED_USERNAME;
			}
			else {
				$res = $res[0];

				if($res['password'] != $this->_password) {
					$this->_id = \Bedrock\Common\Auth::RESULT_FAILED_PASSWORD;
				}
				else {
					$this->_id = $res['id'];
				}
			}

			return $this->_id;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Auth\Exception('Authentication could not be completed.');
		}
	}

	/**
	 * Returns the last response received from an authentication request. The
	 * value returned corresponds to one of the Auth constants (i.e.
	 * Auth::RESULT_FAILED, Auth::RESULT_SUCCESS, etc.).
	 * 
	 * @return integer the last response received
	 */
	public function authResponse() {
		return $this->_response;
	}

	/**
	 * Processes a successful authentication request.
	 */
	public function authSuccess() {
		try {

		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Auth\Exception('Authentication was successful, but the response could not be processed.');
		}
	}

	/**
	 * Processes a failed authentication request.
	 */
	public function authFail() {
		$this->_failCount++;
	}
}