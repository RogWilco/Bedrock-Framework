<?php
/**
 * Email
 * 
 * Provides general email sending functionality.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 06/05/2008
 * @updated 06/05/2008
 */
class Bedrock_Common_Email extends Bedrock {
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
			Bedrock_Common_Logger::logEntry();
			
			// Setup
			$config = Bedrock_Common_Registry::get('config');
			
			$headers = array('From' => $from,
							'To' => $to,
							'Subject' => $subject,
							'Date' => date("r", time()));
			
			$smtpConfig = array('host' => $config->email->smtp,
								'port' => $config->email->port,
								'auth' => true,
								'username' => $config->email->username,
								'password' => $config->email->password);
		
			$smtp = Mail::factory('smtp', $smtpConfig);
			
			Bedrock_Common_Logger::info('Attempting to send an email to "' . $to . '" ...');
			$mail = $smtp->send($to, $headers, $body);
			
			if(PEAR::isError($mail)) {
				throw new Bedrock_Common_Email_Exception($mail->getMessage());
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Bedrock_Common_Email_Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw $ex;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Email_Exception('The email could not be sent.');
		}
	}
}
?>