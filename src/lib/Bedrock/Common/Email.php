<?php
namespace Bedrock\Common;

/**
 * Email
 * 
 * Provides general email sending functionality.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 06/05/2008
 * @updated 07/02/2012
 */
class Email extends \Bedrock {
	/**
	 * Sends an email using the specified details.
	 * 
	 * @param string $from the "from" address
	 * @param string $to the recipient
	 * @param string $subject the subject of the email
	 * @param string $body the body of the email
	 * @return integer the result of the process
	 */
	public static function send($from, $to, $subject, $body) {
		try {
			\Bedrock\Common\Logger::logEntry();
			
			// Setup
			$config = \Bedrock\Common\Registry::get('config');
			
			$headers = array('From' => $from,
							'To' => $to,
							'Subject' => $subject,
							'Date' => date("r", time()));
			
			$smtpConfig = array('host' => $config->email->smtp,
								'port' => $config->email->port,
								'auth' => true,
								'username' => $config->email->username,
								'password' => $config->email->password);
		
			$smtp = \Mail::factory('smtp', $smtpConfig);
			
			\Bedrock\Common\Logger::info('Attempting to send an email to "' . $to . '" ...');
			$mail = $smtp->send($to, $headers, $body);
			
			if(\PEAR::isError($mail)) {
				throw new \Bedrock\Common\Email\Exception($mail->getMessage());
			}
			
			\Bedrock\Common\Logger::logExit();
		}
		catch(\Bedrock\Common\Email\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw $ex;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			\Bedrock\Common\Logger::logExit();
			throw new \Bedrock\Common\Email\Exception('The email could not be sent.');
		}
	}
}