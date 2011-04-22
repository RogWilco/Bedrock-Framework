<?php
/**
 * Provides an interface to Twitter's public APIs.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.0
 * @created 05/06/2009
 * @updated 05/06/2009
 */
class Bedrock_Common_Service_Twitter extends Bedrock_Common {
	protected $_url = 'http://www.twitter.com/';
	protected $_searchUrl = 'http://search.twitter.com/';
	protected $_username = '';
	protected $_password = '';
	protected $_category = 'statuses';

	/**
	 * Initializes a new Twitter object.
	 *
	 * @param string $username a valid Twitter username
	 * @param string $password a valid password
	 * @param array $options any initial properties to use
	 */
	public function __construct($username = '', $password = '', $options = array()) {
		Bedrock_Common_Logger::logEntry();

		try {
			$this->_username = $username;
			$this->_password = $password;

			if(array_key_exists('url', $options)) $this->_url = $options['url'];
			if(array_key_exists('search_url', $options)) $this->_searchUrl = $options['search_url'];

			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A service connection to Twitter could not be initialized.');
		}
	}

	/**
	 * Sets the method category to use and returns a reference to the current
	 * instance (for chainable calls).
	 * 
	 * @param string $name the name of the Twitter method category (as defined in the Twitter API)
	 * @return Bedrock_Common_Service_Twitter a reference to the curren tinstance
	 */
	public function __get($name) {
		Bedrock_Common_Logger::logEntry();

		try {
			$this->category($name);

			Bedrock_Common_Logger::logExit();
			return $this;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('There was a problem while attempting to set the method category to "' . $name . '".');
		}
	}

	/**
	 * Calls the specified Twitter API method within the currently set category.
	 *
	 * @param string $method the method to call
	 * @param array $params any additional parameters provided
	 */
	public function __call($method, $params) {
		Bedrock_Common_Logger::logEntry();

		try {
			$parts = explode($method, '_');
			array_map('ucwords', $parts);
			$realMethod = implode($parts);
			$realMethod = $this->_category . ucwords($method);

			Bedrock_Common_Logger::logExit();
			
			if(method_exists($this, $realMethod)) {
				return call_user_func_array(array(&$this, $realMethod), $params);
			}
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('The specified request could not be made, verify you are using a valid twitter API call and that Bedrock currently supports it.');
		}
	}

	/**
	 * Executes the specified query using the Twitter API.
	 *
	 * @param string $url the URL to use
	 * @param string $method the HTTP method to use
	 * @param string $username a valid Twitter username
	 * @param string $password the matching password
	 * @param array $params additional parameters to send
	 */
	public static function exec($url, $method = 'GET', $username = '', $password = '', $params = array()) {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$options = array();

			if($username && $password) {
				$options[CURLOPT_USERPWD] = $username . ':' . $password;
			}

			switch(strtoupper($method)) {
				default:
				case 'GET':
					return Bedrock_Common_Rest::get($url, $params, $options);
					break;

				case 'POST':
					return Bedrock_Common_Rest::post($url, $params, $options);
					break;
			}
			
			Bedrock_Common_Logger::logExit();
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to execute the specified request.');
		}
	}

	/**
	 * Sets or gets the method category to use.
	 *
	 * @param string $category the category to set, omit to retrieve the current category
	 * @return string the currently set method category
	 */
	public function category($category = '') {
		Bedrock_Common_Logger::logEntry();

		try {
			if(trim($category) != '') {
				$this->_category = $category;
			}

			Bedrock_Common_Logger::logExit();
			return $this->_category;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('There was a problem while attempting to set the method category to "' . $category . '".');
		}
	}

	/**
	 * Ends the session of the authenticating user, returning a null cookie. Use
	 * this method to sign users out of client-facing applications like widgets.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function accountEndSession($format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'account/end_session.' . $format, 'POST', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "account/end_session".');
		}
	}

	/**
	 * Returns the remaining number of API requests available to the requesting
	 * user before the API limit is reached for the current hour. Calls to
	 * rate_limit_status do not count against the rate limit.  If authentication
	 * credentials are provided, the rate limit status for the authenticating
	 * user is returned.  Otherwise, the rate limit status for the requester's
	 * IP address is returned. Learn more about the REST API rate limiting.
	 *
	 * Formats: xml, json
	 * HTTP Method: GET
	 * Requires Authentication: true/false, to determine a user's rate limit status/to determine the requesting IP's rate limit status
	 * API Rate Limited: false
	 *
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function accountRateLimitStatus($format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'account/rate_limit_status.' . $format, 'GET', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "account/rate_limit_status".');
		}
	}

	/**
	 * Sets which device Twitter delivers updates to for the authenticating
	 * user. Sending none as the device parameter will disable IM or SMS
	 * updates.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param string $device must be one of: sms, im, none
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function accountUpdateDeliveryDevice($device, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$params = array('device' => $device);
			$result = self::exec($this->_url . 'account/update_delivery_device.' . $format, 'POST', $this->_username, $this->_password, $params);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "account/update_delivery_device".');
		}
	}

	/**
	 * Sets values that users are able to set under the "Account" tab of their
	 * settings page. Only the parameters specified will be updated.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param string $name maximum of 20 characters
	 * @param string $email maximum of 40 characters (must be a valid email address)
	 * @param string $url maximum of 100 characters (will be prepended with "http://" if not present)
	 * @param string $location maxiumum of 30 characters (the contents are not normalized or geocoded in any way)
	 * @param string $description maximum of 160 characters
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function accountUpdateProfile($name = null, $email = null, $url = null, $location = null, $description = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$params = array();

			if(!empty($name)) $params['name'] = $name;
			if(!empty($email)) $params['email'] = $email;
			if(!empty($url)) $params['url'] = $url;
			if(!empty($location)) $params['location'] = $location;
			if(!empty($description)) $params['description'] = $description;

			$result = self::exec($this->_url . 'account/update_profile.' . $format, 'POST', $this->_username, $this->_password, $params);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "account/update_profile".');
		}
	}

	/**
	 * Updates the authenticating user's profile background image. Note that
	 * this method expects raw multipart data, not a URL to an image.
	 *
	 * Formats: xml, join
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param string $image must be a valid GIF, JPG, or PNG image of less than 800 kilobytes in size (images with width larger than 2048 pixels will be forceably scaled down)
	 * @param boolean $tile if set to true the background image will be displayed tiled, the image will not be tiled otherwise
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function accountUpdateProfileBackgroundImage($image , $tile = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$params = array('image' => $image);

			if(!empty($tile)) $params['tile'] = $tile;

			$result = self::exec($this->_url . 'account/update_profile_background_image.' . $format, 'POST', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "account/update_profile_background_image".');
		}
	}

	/**
	 * Sets one or more hex values that control the color scheme of the
	 * authenticating user's profile page on twitter.com.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param string $profileBackgroundColor must be a valid hexadecimal value
	 * @param string $profileTextColor must be a valid hexadecimal value
	 * @param string $profileLinkColor must be a valid hexadecimal value
	 * @param string $profileSidebarFillColor must be a valid hexadecimal value
	 * @param string $profileSidebarBorderColor must be a valid hexadecimal value
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function accountUpdateProfileColors($profileBackgroundColor = null, $profileTextColor = null, $profileLinkColor = null, $profileSidebarFillColor = null, $profileSidebarBorderColor = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$params = array();

			if(!empty($profileBackgroundColor)) $params['profile_background_color'] = $profileBackgroundColor;
			if(!empty($profileTextColor)) $params['profile_text_color'] = $profileTextColor;
			if(!empty($profileLinkColor)) $params['profile_link_color'] = $profileLinkColor;
			if(!empty($profileSidebarFillColor)) $params['profile_sidebar_fill_color'] = $profileSidebarFillColor;
			if(!empty($profileSidebarBorderColor)) $params['profile_sidebar_border_color'] = $profileSidebarBorderColor;

			$result = self::exec($this->_url . 'account/update_profile_colors.' . $format, 'POST', $this->_username, $this->_password, $params);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "account/update_profile_colors".');
		}
	}

	/**
	 * Updates the authenticating user's profile image. Note that this method
	 * expects raw multipart data, not a URL to an image.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param string $image must be a valid GIF, JPG, or PNG image of less than 700 kilobytes in size (images with width larger than 500 pixels will be scaled down)
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function accountUpdateProfileImage($image, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$params = array('image' => $image);

			$result = self::exec($this->_url . 'account/update_profile_image.' . $format, 'POST', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "account/update_profile_image".');
		}
	}

	/**
	 * Returns an HTTP 200 OK response code and a representation of the
	 * requesting user if authentication was successful; returns a 401 status
	 * code and an error message if not. Use this method to test if supplied
	 * user credentials are valid.
	 *
	 * Formats: xml, json
	 * HTTP Method: GET
	 * Require Authentication: true
	 * API Rate Limited: true
	 *
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function accountVerifyCredentials($format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'account/verify_credentials.' . $format, 'GET', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "account/verify_credentials".');
		}
	}

	/**
	 * Blocks the user specified in the ID parameter as the authenticating user.
	 * Returns the blocked user in the requested format when successful. You can
	 * find out more about blocking in the Twitter Support Knowledge Base.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param mixed $id the ID or screen name of a user to block
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function blocksCreate($id, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'blocks/create/' . $id . '.' . $format, 'POST', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "blocks/create".');
		}
	}

	/**
	 * Un-blocks the user specified in the ID parameter for the authenticating
	 * user. Returns the un-blocked user in the requested format when
	 * successful.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param mixed $id the ID or screen_name of the user to un-block
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function blocksDestroy($id, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'blocks/destroy/' . $id . '.' . $format, 'POST', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "blocks/destroy".');
		}
	}

	/**
	 * Returns a list of the 20 most recent direct messages sent to the
	 * authenticating user.  The XML and JSON versions include detailed
	 * information about the sending and recipient users.
	 * 
	 * Formats: xml, json, rss, atom
	 * HTTP Methods: GET
	 * Requires Authentication: true
	 * API Rate Limited: 1 call per request
	 *
	 * @param integer $sinceId returns only direct messages with an ID greater than (that is, more recent than) the specified ID
	 * @param integer $maxId returns only statuses with an ID less than (that is, older than) or equal to the specified ID
	 * @param integer $count specifies the number of statuses to retrieve. May not be greater than 200
	 * @param integer $page specifies the page of direct messages to retrieve
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function directMessages($sinceId = null, $maxId = null, $count = null, $page = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$params = array();

			if(!empty($sinceId)) $params['since_id'] = $sinceId;
			if(!empty($maxId)) $params['max_id'] = $maxId;
			if(!empty($count)) $params['count'] = $count;
			if(!empty($page)) $params['page'] = $page;

			$result = self::exec($this->_url . 'direct_messages.' . $format, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "direct_messages".');
		}
	}

	/**
	 * Destroys the direct message specified in the required ID parameter. The
	 * authenticating user must be the recipient of the specified direct
	 * message.
	 *
	 * Formats: xml, json
	 * HTTP Methods: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param integer $id the ID of the direct message to destroy
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function directMessagesDestroy($id, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'direct_messages/destroy/' . $id . '.' . $format, 'POST', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "direct_messages/destroy".');
		}
	}

	/**
	 * Sends a new direct message to the specified user from the authenticating
	 * user. Requires both the user and text parameters. Request must be a POST.
	 * Returns the sent message in the requested format when successful.
	 *
	 * Formats: xml, json
	 * HTTP Methods: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param mixed $user the ID or screen name of the recipient user
	 * @param string $text the text of your direct message (be sure to URL encode as necessary, and keep it under 140 characters)
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function directMessagesNew($user, $text, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$params = array('user' => $user, 'text' => $text);

			$result = self::exec($this->_url . 'direct_messages/new.' . $format, 'POST', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "direct_messages/new".');
		}
	}

	/**
	 * Returns a list of the 20 most recent direct messages sent to the
	 * authenticating user. The XML and JSON versions include detailed
	 * information about the sending and recipient users.
	 *
	 * Formats: xml, json, rss, atom
	 * HTTP Methods: GET
	 * Requires Authentication: true
	 * API Rate Limited: 1 call per request
	 *
	 * @param integer $sinceId returns only direct messages with an ID greater than (that is, more recent than) the specified ID
	 * @param integer $maxId returns only statuses with an ID less than (that is, older than) or equal to the specified ID
	 * @param integer $page specifies the page of direct messages to retrieve
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function directMessagesSent($sinceId = null, $maxId = null, $page = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$params = array();

			if(!empty($sinceId)) $params['since_id'] = $sinceId;
			if(!empty($maxId)) $params['max_id'] = $maxId;
			if(!empty($page)) $params['page'] = $page;

			$result = self::exec($this->_url . 'direct_messages/sent.' . $format, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "direct_messages/sent".');
		}
	}

	/**
	 * Returns the 20 most recent favorite statuses for the authenticating user
	 * or user specified by the ID parameter in the requested format.
	 *
	 * Formats: xml, json, rss, atom
	 * HTTP Method: GET
	 * Requires Authentication: true
	 * API Rate Limited: 1 call per request
	 *
	 * @param mixed $id the ID or screen name of the user for whom to request a list of favorite statuses
	 * @param integer $page specifies the page of favorites to retrieve
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function favorites($id = null, $page = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$url = $this->_url . 'favorites';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			else {
				$url .= '.' . $format;
			}

			if(!empty($page)) $params['page'] = $page;

			$result = self::exec($url, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "favorites".');
		}
	}

	/**
	 * Favorites the status specified in the ID parameter as the authenticating
	 * user. Returns the favorite status when successful.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param integer $id the ID of the status to favorite
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function favoritesCreate($id, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'favorites/create/' . $id . '.' . $format, 'POST', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "favorites/create".');
		}
	}

	/**
	 * Un-favorites the status specified in the ID parameter as the
	 * authenticating user. Returns the un-favorited status in the requested
	 * format when successful.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param integer $id the ID of the status to un-favorite
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function favoritesDestroy($id, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'favorites/destroy/' . $id . '.' . $format, 'POST', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "favorites/destroy".');
		}
	}

	/**
	 * Returns an array of numeric IDs for every user following the specified
	 * user.
	 *
	 * Formats: xml, json
	 * HTTP Method: GET
	 * Requires Authentication: false
	 * API Rate Limited: 1 call per request
	 *
	 * @param mixed $id the ID or screen_name of the user to retrieve the friends ID list for
	 * @param integer $userId specfies the ID of the user for whom to return the friends list (helpful for disambiguating when a valid user ID is also a valid screen name)
	 * @param string $screenName specfies the screen name of the user for whom to return the friends list (helpful for disambiguating when a valid screen name is also a user ID)
	 * @param integer $page specifies the page number of the results beginning at 1 (a single page contains 5000 ids, this is recommended for users with large ID lists, if not provided all ids are returned)
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function followersId($id = null, $userId = null, $screenName = null, $page = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$url = $this->_url . 'followers/id';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			elseif(!empty($userId)) {
				$url .= '.' . $format;
				$params['user_id'] = $userId;
			}
			elseif(!empty($screenName)) {
				$url .= '.' . $format;
				$params['screen_name'] = $screenName;
			}
			else {
				throw new Bedrock_Common_Services_Twitter_Exception('No user identifier specified, cannot complete query.');
			}

			if(!empty($page)) $params['page'] = $page;

			$result = self::exec($url, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "followers/id".');
		}
	}

	/**
	 * Returns an array of IDs for every user the specified user is following.
	 *
	 * Formats: xml, json
	 * HTTP Method: GET
	 * Requires Authentication: false
	 * API Rate Limited: 1 call per request
	 *
	 * @param mixed $id the ID or screen_name of the user to retrieve the friends ID list for
	 * @param integer $userId specfies the ID of the user for whom to return the friends list. Helpful for disambiguating when a valid user ID is also a valid screen name
	 * @param string $screenName specfies the screen name of the user for whom to return the friends list. Helpful for disambiguating when a valid screen name is also a user ID
	 * @param integer $page specifies the page number of the results beginning at 1. A single page contains 5000 ids. This is recommended for users with large ID lists. If not provided all ids are returned
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function friendsIds($id = null, $userId = null, $screenName = null, $page = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$url = $this->_url . 'friends/ids';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			elseif(!empty($userId)) {
				$url .= '.' . $format;
				$params['user_id'] = $userId;
			}
			elseif(!empty($screenName)) {
				$url .= '.' . $format;
				$params['screen_name'] = $screenName;
			}
			else {
				throw new Bedrock_Common_Services_Twitter_Exception('No user identifier specified, cannot complete query.');
			}

			if(!empty($page)) $params['page'] = $page;

			$result = self::exec($url, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "friends/ids".');
		}
	}

	/**
	 * Allows the authenticating users to follow the user specified in the ID
	 * parameter. Returns the befriended user in the requested format when
	 * successful. Returns a string describing the failure condition when
	 * unsuccessful.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param mixed $id the ID or screen name of the user to befriend
	 * @param integer $userId specfies the ID of the user to befriend (helpful for disambiguating when a valid user ID is also a valid screen name)
	 * @param string $screenName specfies the screen name of the user to befriend (helpful for disambiguating when a valid screen name is also a user ID)
	 * @param boolean $follow enable notifications for the target user in addition to becoming friends
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function friendshipsCreate($id = null, $userId = null, $screenName = null, $follow = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$url = $this->_url . 'friendships/create';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			elseif(!empty($userId)) {
				$url .= '.' . $format;
				$params['user_id'] = $userId;
			}
			elseif(!empty($screenName)) {
				$url .= '.' . $format;
				$params['screen_name'] = $screenName;
			}
			else {
				throw new Bedrock_Common_Services_Twitter_Exception('No user identifier specified, cannot complete query.');
			}

			if(!empty($follow)) $params['follow'] = $follow;

			$result = self::exec($url, 'POST', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "friendships/create".');
		}
	}

	/**
	 * Allows the authenticating users to unfollow the user specified in the ID
	 * parameter. Returns the unfollowed user in the requested format when
	 * successful. Returns a string describing the failure condition when
	 * unsuccessful.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param mixed $id the ID or screen name of the user to unfollow
	 * @param integer $userId specfies the ID of the user to unfollow. Helpful for disambiguating when a valid user ID is also a valid screen name
	 * @param string $screenName specfies the screen name of the user to unfollow. Helpful for disambiguating when a valid screen name is also a user ID
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function friendshipsDestroy($id = null, $userId = null, $screenName = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$url = $this->_url . 'friendships/destroy';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			elseif(!empty($userId)) {
				$url .= '.' . $format;
				$params['user_id'] = $userId;
			}
			elseif(!empty($screenName)) {
				$url .= '.' . $format;
				$params['screen_name'] = $screenName;
			}
			else {
				throw new Bedrock_Common_Services_Twitter_Exception('No user identifier specified, cannot complete query.');
			}

			$result = self::exec($url, 'POST', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "friendships/destroy".');
		}
	}

	/**
	 * Tests for the existance of friendship between two users. Will return true
	 * if user_a follows user_b, otherwise will return false.
	 *
	 * Formats: xml, json
	 * HTTP Method: GET
	 * Requires Authentication: true
	 * API Rate Limited: true
	 *
	 * @param mixed $userA the ID or screen_name of the subject user
	 * @param mixed $userB the ID or screen_name of the user to test for following
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function friendshipsExists($userA, $userB, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$params = array('user_a' => $userA, 'user_b' => $userB);

			$result = self::exec($this->_url . 'friendships/exists.' . $format, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "friendships/exists".');
		}
	}

	/**
	 * Returns the string "ok" in the requested format with a 200 OK HTTP
	 * status code.
	 *
	 * Formats: xml, json
	 * HTTP Method: GET
	 * Requires Authentication: false
	 * API Rate Limited: false
	 *
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function helpTest($format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'help/test.' . $format, 'GET', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "help/test".');
		}
	}

	/**
	 * Enables device notifications for updates from the specified user. Returns
	 * the specified user when successful.
	 *
	 * Format: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param mixed $id the ID or screen name of the user to follow with device updates
	 * @param integer $userId specfies the ID of the user to follow with device updates (helpful for disambiguating when a valid user ID is also a valid screen name)
	 * @param string $screenName specfies the screen name of the user to follow with device updates (helpful for disambiguating when a valid screen name is also a user ID)
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function notificationsFollow($id = null, $userId = null, $screenName = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$url = 'notifications/follow';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			elseif(!empty($userId)) {
				$url .= '.' . $format;
				$params['user_id'] = $userId;
			}
			elseif(!empty($screenName)) {
				$url .= '.' . $format;
				$params['screen_name'] = $screenName;
			}
			else {
				throw new Bedrock_Common_Services_Twitter_Exception('No user identifier specified, cannot complete query.');
			}

			$result = self::exec($url, 'POST', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "notifications/follow".');
		}
	}

	/**
	 * Disables notifications for updates from the specified user to the
	 * authenticating user. Returns the specified user when successful.
	 *
	 * Formats: xml, json
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param mixed $id the ID or screen name of the user to disable device notifications
	 * @param integer $userId specfies the ID of the user to disable device notifications (helpful for disambiguating when a valid user ID is also a valid screen name)
	 * @param string $screenName specfies the screen name of the user of the user to disable device notifications (helpful for disambiguating when a valid screen name is also a user ID)
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function notificationsLeave($id = null, $userId = null, $screenName = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$url = 'notifications/leave';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			elseif(!empty($userId)) {
				$url .= '.' . $format;
				$params['user_id'] = $userId;
			}
			elseif(!empty($screenName)) {
				$url .= '.' . $format;
				$params['screen_name'] = $screenName;
			}
			else {
				throw new Bedrock_Common_Services_Twitter_Exception('No user identifier specified, cannot complete query.');
			}

			$result = self::exec($url, 'POST', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "notifications/leave".');
		}
	}

	/**
	 * Returns tweets that match a specified query.
	 *
	 * Formats: json, atom
	 * HTTP Method: GET
	 * Requires Authentication: false
	 * API Rate Limited: 1 call per request
	 *
	 * @param string $callback if supplied, the response will use the JSONP format with a callback of the given name (only available for JSON format)
	 * @param string $lang restricts tweets to the given language, given by an ISO 639-1 code
	 * @param integer $rpp the number of tweets to return per page, up to a max of 100
	 * @param integer $page the page number (starting at 1) to return, up to a max of roughly 1500 results (based on rpp * page (note: there are pagination limits)
	 * @param integer $sinceId returns tweets with status ids greater than the given id
	 * @param string $geocode returns tweets by users located within a given radius of the given latitude/longitude, where the user's location is taken from their Twitter profile; the parameter value is specified by "latitide,longitude,radius", where radius units must be specified as either "mi" (miles) or "km" (kilometers) (note that you cannot use the near operator via the API to geocode arbitrary locations; however you can use this geocode parameter to search near geocodes directly)
	 * @param boolean $showUser when true, prepends "<user>:" to the beginning of the tweet (this is useful for readers that do not display Atom's author field, the default is false)
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function search($callback = null, $lang = null, $rpp = null, $page = null, $sinceId = null, $geocode = null, $showUser = null, $format = 'json') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$params = array();

			if(!empty($callback)) $params['callback'] = $callback;
			if(!empty($lang)) $params['lang'] = $lang;
			if(!empty($rpp)) $params['rpp'] = $rpp;
			if(!empty($page)) $params['page'] = $page;
			if(!empty($sinceId)) $params['since_id'] = $sinceId;
			if(!empty($geocode)) $params['geocode'] = $geocode;
			if(!empty($showUser)) $params['show_user'] = $showUser;

			$result = self::exec($this->_searchUrl . 'search.' . $format, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "search".');
		}
	}

	/**
	 * Destroys the status specified by the required ID parameter.  The
	 * authenticating user must be the author of the specified status.
	 *
	 * Formats: xml, json, rss, atom
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param integer $id the ID of the status to destroy
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function statusesDestroy($id, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'statuses/destroy/' . $id . '.' . $format, 'POST', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "statuses/destroy".');
		}
	}

	/**
	 * Returns the authenticating user's followers, each with current status
	 * inline.  They are ordered by the order in which they joined Twitter.
	 *
	 * Formats: xml, json, rss, atom
	 * HTTP Method: GET
	 * Requires Authentication: true
	 * API Rate Limited: 1 call per request
	 *
	 * @param mixed $id the ID or screen name of the user for whom to request a list of followers
	 * @param integer $userId specfies the ID of the user for whom to return the list of followers (helpful for disambiguating when a valid user ID is also a valid screen name)
	 * @param string $screenName specfies the screen name of the user for whom to return the list of followers (helpful for disambiguating when a valid screen name is also a user ID)
	 * @param integer $page specifies the page to retrieve
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function statusesFollowers($id = null, $userId = null, $screenName = null, $page = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$url = $this->_url . 'statuses/followers';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			elseif(!empty($userId)) {
				$url .= '.' . $format;
				$params['user_id'] = $userId;
			}
			elseif(!empty($screenName)) {
				$url .= '.' . $format;
				$params['screen_name'] = $screenName;
			}
			else {
				throw new Bedrock_Common_Services_Twitter_Exception('No user identifier specified, cannot complete query.');
			}

			if(!empty($page)) $params['page'] = $page;

			$result = self::exec($url, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "statuses/followers".');
		}
	}

	/**
	 * Returns a user's friends, each with current status inline. They are
	 * ordered by the order in which they were added as friends. Defaults to the
	 * authenticated user's friends. It's also possible to request another
	 * user's friends list via the id parameter.
	 *
	 * Formats: xml, json
	 * HTTP Method: GET
	 * Requires Authentication: false
	 * API Rate Limited: 1 call per request
	 *
	 * @param mixed $id the ID or screen name of the user for whom to request a list of friends
	 * @param integer $userIdspecifies the ID of the user for whome to return the list of friends (helpful for disambiguating when a valid user ID is also a valid screen name)
	 * @param string $screenName specifies the screen name of the user for whom to return the list of friends (helpful for disambiguating when a valid screen name is also a user ID)
	 * @param integer $page specifies the page of friends to receive
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function statusesFriends($id = null, $userId = null, $screenName = null, $page = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$url = $this->_url . 'statuses/friends';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			elseif(!empty($userId)) {
				$url .= '.' . $format;
				$params['user_id'] = $userId;
			}
			elseif(!empty($screenName)) {
				$url .= '.' . $format;
				$params['screen_name'] = $screenName;
			}
			else {
				throw new Bedrock_Common_Services_Twitter_Exception('No user identifier specified, cannot complete query.');
			}

			if(!empty($page)) $params['page'] = $page;

			$result = self::exec($url, 'GET', $this->_username, $this->_password, $params);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "statuses/friends".');
		}
	}

	/**
	 * Returns the 20 most recent statuses posted by the authenticating user and
	 * that user's friends. This is the equivalent of /timeline/home on the Web.
	 * 
	 * Formats: xml, json, rss, atom
	 * HTTP Method: GET
	 * Requires Authentication: true
	 * API Rate Limited: 1 call per request
	 *
	 * @param integer $sinceId returns only statuses with an ID greater than (that is, more recent than) the specified ID
	 * @param integer $maxId returns only statuses with an ID less than (that is, older than) or equal to the specified ID
	 * @param integer $count specifies the number of statuses to retrieve. May not be greater than 200
	 * @param integer $page pecifies the page of results to retrieve (note: there are pagination limits)
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function statusesFriendsTimeline($sinceId = null, $maxId = null, $count = null, $page = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$params = array();

			if(!empty($sinceId)) $params['since_id'] = $sinceId;
			if(!empty($maxId)) $params['max_id'] = $maxId;
			if(!empty($count)) $params['count'] = $count;
			if(!empty($page)) $params['page'] = $page;

			$result = self::exec($this->_url . 'statuses/friends_timeline' . $format, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "statuses/friends_timeline".');
		}
	}

	/**
	 * Returns the 20 most recent mentions (status containing @username) for the
	 * authenticating user.
	 *
	 * Formats: xml, json, rss, atom
	 * HTTP Method: GET
	 * Requires Authentication: true
	 * API Rate Limited: 1 call per request
	 *
	 * @param integer $sinceId returns only statuses with an ID greater than (that is, more recent than) the specified ID
	 * @param integer $maxId returns only statuses with an ID less than (that is, older than) or equal to the specified ID
	 * @param integer $count specifies the number of statuses to retrieve (may not be greater than 200)
	 * @param integer $page specifies the page or results to retrieve (there are pagination limits)
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function statusesMentions($sinceId = null, $maxId = null, $count = null, $page = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$url = $this->_url . 'statuses/mentions.' . $format;
			$params = array();

			if(!empty($sinceId)) $params['since_id'] = $sinceId;
			if(!empty($maxId)) $params['max_id'] = $maxId;
			if(!empty($count)) $params['count'] = $count;
			if(!empty($page)) $params['page'] = $page;

			$result = self::exec($url, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "statuses/mentions".');
		}
	}

	/**
	 * Returns the 20 most recent statuses from non-protected users who have set
	 * a custom user icon. The public timeline is cached for 60 seconds so
	 * requesting it more often than that is a waste of resources.
	 *
	 * Formats: xml, json, rss, atom
	 * HTTP Methods: GET
	 * Requires Authentication: false
	 * API Rate Limited: false
	 *
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function statusesPublicTimeline($format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'statuses/public_timeline.' . $format, 'GET', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "statuses/public_timeline".');
		}
	}

	/**
	 * Returns a single status, specified by the id parameter below. The
	 * status's author will be returned inline.
	 *
	 * Formats: xml, json, rss, atom
	 * HTTP Method: GET
	 * Requires Authentication: false, unless the author of the status is protected
	 * API Rate Limited: 1 call per request
	 *
	 * @param integer $id the numerical ID of the status to retrieve
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function statusesShow($id, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_url . 'statuses/show/' . $id . '.' . $format, 'GET', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "statuses/show".');
		}
	}

	/**
	 * Updates the authenticating user's status. Requires the status parameter
	 * specified below. Request must be a POST. A status update with text
	 * identical to the authenticating user's current status will be ignored to
	 * prevent duplicates.
	 *
	 * Formats: xml, json, rss, atom
	 * HTTP Method: POST
	 * Requires Authentication: true
	 * API Rate Limited: false
	 *
	 * @param string $status the text of your status update, URL encode as necessary, statuses over 140 characters will be forceably truncated
	 * @param integer $inReplyToStatusId the ID of an existing status that the update is in reply to
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function statusesUpdate($status, $inReplyToStatusId = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$params = array('status' => $status);

			if(!empty($inReplyToStatusId)) {
				$params['in_reply_to_status_id'] = $inReplyToStatusId;
			}

			$result = self::exec($this->_url . 'statuses/update.' . $format, 'POST', $this->_username, $this->_password, $params);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "statuses/update".');
		}
	}

	/**
	 * Returns the 20 most recent statuses posted from the authenticating user.
	 * It's also possible to request another user's timeline via the id
	 * parameter. This is the equivalent of the Web /<user> page for your own
	 * user, or the profile page for a third party.
	 *
	 * Formats: xml, json, rss, atom
	 * HTTP Method: GET
	 * Requires Authentication: true, if requesting a protected user's timeline
	 * API Rate Limited: 1 call per request
	 *
	 * @param mixed $id specifies the ID or screen name of the user for whom to return the user_timeline
	 * @param integer $userId specifies the ID of the user for whom to return the user_timeline (helpful for disambiguating when a valid user ID is also a valid screen name)
	 * @param string $screenName specifies the screen name of the user for whom to return the user_timeline (helpful for disambiguating when a valid screen name is also a user ID)
	 * @param integer $sinceId returns only statuses with an ID greater than (that is, more recent than) the specified ID
	 * @param integer $maxId returns only statuses with an ID less than (that is, older than) or equal to the specified ID
	 * @param integer $count specifies the number of statuses to retrieve (may not be greater than 200)
	 * @param integer $page specifies the page of results to retrieve
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function statusesUserTimeline($id = null, $userId = null, $screenName = null, $sinceId = null, $maxId = null, $count = null, $page = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$url = $this->_url . 'statuses/user_timeline';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			elseif(!empty($userId)) {
				$url .= '.' . $format;
				$params['user_id'] = $userId;
			}
			elseif(!empty($screenName)) {
				$url .= '.' . $format;
				$params['screen_name'] = $screenName;
			}
			else {
				throw new Bedrock_Common_Services_Twitter_Exception('No user identifier specified, cannot complete query.');
			}

			if(!empty($sinceId)) $params['since_id'] = $sinceId;
			if(!empty($maxId)) $params['max_id'] = $maxId;
			if(!empty($count)) $params['count'] = $count;
			if(!empty($page)) $params['page'] = $page;

			$result = self::exec($url, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "statuses/user_timeline".');
		}
	}

	/**
	 * Returns the top ten topics that are currently trending on Twitter. The
	 * response includes the time of the request, the name of each trend, and
	 * the url to the Twitter Search results page for that topic.
	 *
	 * Formats: json
	 * HTTP Method: GET
	 * Requires Authentication: false
	 * API Rate Limited: 1 call request
	 *
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function trends($format = 'json') {
		Bedrock_Common_Logger::logEntry();

		try {
			$result = self::exec($this->_searchUrl . 'trends.' . $format, 'GET', $this->_username, $this->_password);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "trends".');
		}
	}

	/**
	 * Returns the current top 10 trending topics on Twitter. The response
	 * includes the time of the request, the name of each trending topic, and
	 * query used on Twitter Search results page for that topic.
	 *
	 * Formats: json
	 * HTTP Method: GET
	 * Requires Authentication: false
	 * API Rate Limited: 1 call per request
	 *
	 * @param string $exclude setting this equal to hashtags will remove all hashtags from the trends list
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function trendsCurrent($exclude = null, $format = 'json') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$params = array();

			if(!empty($exclude)) $params['exclude'] = $exclude;

			$result = self::exec($this->_searchUrl . 'trends/current.' . $format, 'GET', $this->_username, $this->_password, $params);
			
			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "trends/current".');
		}
	}

	/**
	 * Returns the top 20 trending topics for each hour in a given day.
	 *
	 * Formats: json
	 * HTTP Method: GET
	 * Requires Authentication: false
	 * API Rate Limited: 1 call per request
	 *
	 * @param string $date permits specifying a start date for the report (the date should be formatted YYYY-MM-DD)
	 * @param string $exclude setting this equal to hashtags will remove all hashtags from the trends list
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function trendsDaily($date = null, $exclude = null, $format = 'json') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$params = array();

			if(!empty($date)) $params['date'] = $date;
			if(!empty($exclude)) $params['exclude'] = $exclude;

			$result = self::exec($this->_searchUrl . 'trends/daily.' . $format, 'GET', $this->_username, $this->_password, $params);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "trends/daily".');
		}
	}

	/**
	 * Returns the top 30 trending topics for each day in a given week.
	 *
	 * Formats: json
	 * HTTP Method: GET
	 * Requires Authentication: false
	 * API Rate Limited: 1 call per request
	 *
	 * @param string $date permits specifying a start date for the report (the date should be formatted YYYY-MM-DD)
	 * @param string $exclude etting this equal to hashtags will remove all hashtags from the trends list
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function trendsWeekly($date = null, $exclude = null, $format = 'json') {
		Bedrock_Common_Logger::logEntry();
		
		try {
			// Setup
			$params = array();

			if(!empty($date)) $params['date'] = $date;
			if(!empty($exclude)) $params['exclude'] = $exclude;

			$result = self::exec($this->_searchUrl . 'trends/weekly.' . $format, 'GET', $this->_username, $this->_password, $params);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "trends/weekly".');
		}
	}

	/**
	 * Returns extended information of a given user, specified by ID or screen
	 * name as per the required id parameter.  The author's most recent status
	 * will be returned inline.
	 *
	 * Formats: xml, json, rss, atom
	 * HTTP Method: GET
	 * Requires Authentication: false, unless the user is protected
	 * API Rate Limited: 1 call per request
	 *
	 * @param mixed $id the ID or screen name of a user
	 * @param integer $userId specifies the ID of the user to return (helpful for disambiguating when a valid user ID is also a valid screen name)
	 * @param <type> $screenName specifies the screen name of the user to return (helpful for disambiguating when a valid screen name is also a user ID)
	 * @param string $format the format with which the response should use
	 * @return string the formatted response
	 */
	protected function usersShow($id = null, $userId = null, $screenName = null, $format = 'xml') {
		Bedrock_Common_Logger::logEntry();

		try {
			// Setup
			$url = $this->_searchUrl . 'users/show';
			$params = array();

			if(!empty($id)) {
				$url .= '/' . $id . '.' . $format;
			}
			elseif(!empty($userId)) {
				$url .= '.' . $format;
				$params['user_id'] = $userId;
			}
			elseif(!empty($screenName)) {
				$url .= '.' . $format;
				$params['screen_name'] = $screenName;
			}
			else {
				throw new Bedrock_Common_Services_Twitter_Exception('No user identifier specified, cannot complete query.');
			}

			$result = self::exec($url, 'GET', $this->_username, $this->_password, $params);

			Bedrock_Common_Logger::logExit();
			return $result;
		}
		catch(Exception $ex) {
			Bedrock_Common_Logger::exception($ex);
			Bedrock_Common_Logger::logExit();
			throw new Bedrock_Common_Service_Twitter_Exception('A problem was encountered while attempting to make the request "users/show".');
		}
	}
}
?>
