<?php
/**
 * Installer class for handling initial installation of a Bedrock application.
 * 
 * @package Bedrock
 * @author Nick Williams
 * @version 1.0.3
 * @created 04/08/2009
 * @updated 08/22/2012
 */
class Installer {
	protected static $_data = array();
	protected static $_errors = array();
	protected static $_output = true;
	protected static $_outputFormat = 'HTML';	// Supported: "HTML", "JSON", "text"
	protected static $_steps = array(
		'step_start' => false,
		'step_01' => false,
		'step_02' => false,
		'step_03' => false,
		'step_04' => false,
		'step_install' => false
	);
	protected static $_requirements = array(
		'php' => '5.3.0',
		'os' => array(
			'CYGWIN_NT-5.1',
			'Darwin',
			'FreeBSD',
//			'HP-UX',
//			'IRIX64',
			'Linux',
			'NetBSD',
			'OpenBSD',
			'SunOS',
			'Unix'
//			'WINNT',
//			'WIN32',
//			'Windows'
		)
	);

	/**
	 * Initializes the installer process, and loads any cached data.
	 */
	public static function init() {
		session_start();

		if($_SESSION['installer']['data']) {
			self::$_data = $_SESSION['installer']['data'];
		}
		else {
			chdir('..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR);
			$systemRoot = getcwd() . DIRECTORY_SEPARATOR;
			$webRoot = $_SERVER['REQUEST_URI'];
			$webRoot = explode('/', $webRoot);
			array_pop($webRoot);
			array_pop($webRoot);
			$webRoot = '/' . implode('/', $webRoot);
			
			self::$_data = array(
				'app_name' => 'Bedrock Application',
				'app_version' => '1.0.0',
				'app_namespace' => 'Application',
				'bedrock_version' => '0.3.0',
				'root' => 'default',
				'root_web' => $webRoot,
				'root_system' => $systemRoot,
				'root_cfg' => $systemRoot . 'cfg' . DIRECTORY_SEPARATOR,
				'root_lib' => $systemRoot . 'lib' . DIRECTORY_SEPARATOR,
				'root_log' => $systemRoot . 'log' . DIRECTORY_SEPARATOR,
				'root_pub' => $systemRoot . 'pub' . DIRECTORY_SEPARATOR,
				'database_type' => 'none',
				'database_address' => '',
				'database_name' => '',
				'database_prefix' => '',
				'database_username' => '',
				'database_password' => '',
				'friendly' => true,
				'logto' => array('text'),
				'growl_address' => ''
			);
		}

		if($_SESSION['installer']['errors']) {
			self::$_errors = $_SESSION['installer']['errors'];
		}

		if($_SESSION['installer']['steps']) {
			self::$_steps = $_SESSION['installer']['steps'];
		}
	}

	/**
	 * Caches the currently stored installer data to the session.
	 */
	public static function cache() {
		$_SESSION['installer']['data'] = self::$_data;
		$_SESSION['installer']['errors'] = self::$_errors;
		$_SESSION['installer']['steps'] = self::$_steps;
	}

	/**
	 * Resets the installation process and all cached data.
	 */
	public static function reset() {
		self::$_data = array();
		self::$_errors = array();
		self::$_steps = array();
		unset($_SESSION['installer']);
	}

    /**
     * Initiates the installation process after all settings have been set.
     *
     * @param boolean $output whether or not the installation process should display any output
     *
     * @throws Exception if any step in the installation process fails
     * @return boolean whether or not the process was successful
     */
	public static function install($output = true) {
		try {
			// Setup
			$xmlString = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n";
			$xmlObj = null;
			$htaccess = '';
			self::$_output = $output;
			date_default_timezone_set(self::$_data['timezone']);

			ini_set('output_buffering', false);
			ini_set('implicit_flush', 'true');

			// Install
			self::out('Installing Bedrock Framework ' . self::$_data['bedrock_version'], 'status');

            // Install: Directories
			self::out('Creating directory structure...', 'info');

			self::out('- Creating configuration directory.', 'info');
			if(!is_dir(self::$_data['root_cfg'])) {
				mkdir(self::$_data['root_cfg'], 0775);
			}

			self::out('- Creating log directory.', 'info');
			if(!is_dir(self::$_data['root_log'])) {
				mkdir(self::$_data['root_log'], 0775);
			}

			// Install: XML File
			self::out('Building XML File...', 'info');
			$xmlString .= '<config></config>';
			$xmlObj = simplexml_load_string($xmlString);
			$xmlObj->addChild('main');

			// Install: Application Details
			self::out('- Writing application details.', 'info');
			$xmlObj->main->addChild('meta');
			$xmlObj->main->meta->addChild('title', self::$_data['app_name']);
			$xmlObj->main->meta->addChild('namespace', self::$_data['app_namespace']);
			$xmlObj->main->meta->addChild('version');
			$xmlObj->main->meta->version->addChild('application', self::$_data['app_version']);
			$xmlObj->main->meta->version->addChild('bedrock', self::$_data['bedrock_version']);
			$xmlObj->main->meta->addChild('timezone', self::$_data['timezone']);
			$xmlObj->main->meta->addChild('dates');
			$xmlObj->main->meta->dates->addChild('installed', date('Y-m-d H:i:s'));
			$xmlObj->main->meta->dates->addChild('updated', date('Y-m-d H:i:s'));

			// Install: Directory Settings
			self::out('- Writing directory settings.', 'info');
			$xmlObj->main->addChild('root');
			$xmlObj->main->root->addChild('web', self::$_data['root_web']);
			$xmlObj->main->root->addChild('system', self::$_data['root_system']);
			$xmlObj->main->root->addChild('cfg', self::$_data['root_cfg']);
			$xmlObj->main->root->addChild('lib', self::$_data['root_lib']);
			$xmlObj->main->root->addChild('log', self::$_data['root_log']);
			$xmlObj->main->root->addChild('pub', self::$_data['root_pub']);

			// Install: Database Settings
			self::out('- Writing database configuration.', 'info');
			$xmlObj->main->addChild('database');
			$xmlObj->main->database->addChild('type', 'mysql');
			$xmlObj->main->database->addChild('host', self::$_data['database_address']);
			$xmlObj->main->database->addChild('dbname', self::$_data['database_name']);
			$xmlObj->main->database->addChild('username', self::$_data['database_username']);
			$xmlObj->main->database->addChild('password', self::$_data['database_password']);

			// Install: Additional Settings
			self::out('- Writing logger configuration.', 'info');
			$xmlObj->main->addChild('logger');
			$xmlObj->main->logger->addChild('targets');
			$xmlObj->main->logger->targets->addChild('system');
			$xmlObj->main->logger->targets->system->addChild('active', '0');
			$xmlObj->main->logger->targets->system->addChild('level', 'warn');
			$xmlObj->main->logger->targets->addChild('file');
			$xmlObj->main->logger->targets->file->addChild('active', '0');
			$xmlObj->main->logger->targets->file->addChild('level', 'warn');
			$xmlObj->main->logger->targets->addChild('firephp');
			$xmlObj->main->logger->targets->firephp->addChild('level', 'traverse');
			$xmlObj->main->logger->targets->firephp->addChild('active', '0');
			$xmlObj->main->logger->targets->addChild('growl');
			$xmlObj->main->logger->targets->growl->addChild('level', 'warn');
			$xmlObj->main->logger->targets->growl->addChild('active', '0');

			foreach(self::$_data['logto'] as $target) {
				if($xmlObj->main->logger->targets->$target) {
					$xmlObj->main->logger->targets->$target->active = '1';
				}
			}

			$xmlObj->main->addChild('env');
			$xmlObj->main->env->addChild('os', '');
			$xmlObj->main->env->addChild('file');
			$xmlObj->main->env->file->addChild('maxsize', 2097152);
			$xmlObj->main->env->addChild('error', 'E_ALL - E_NOTICE');
			$xmlObj->main->addChild('growl');
			$xmlObj->main->growl->addChild('host', self::$_data['growl_address']);
			$xmlObj->main->addChild('cookie');
			$xmlObj->main->cookie->addChild('name', self::$_data['app_namespace']);
			$xmlObj->main->cookie->addChild('life', 172800);

			$dom = dom_import_simpleXml($xmlObj)->ownerDocument;
			$dom->formatOutput = true;

			if(!$dom->save($xmlObj->main->root->cfg . 'application.xml')) {
				throw new Exception('Error: Configuration XML file could not be saved to "' . $xmlObj->main->root->config . 'application.xml"');
			}

			// Install: .htaccess
			if(self::$_data['friendly']) {
				self::out('Building .htaccess file...', 'info');
				$htaccess .= 'RewriteEngine on' . "\n";
				$htaccess .= "\n";
				$htaccess .= 'RewriteCond %{REQUEST_FILENAME} !-f' . "\n";
				$htaccess .= 'RewriteCond %{REQUEST_FILENAME} !-d' . "\n";
				$htaccess .= "\n";
				$htaccess .= 'RewriteRule ^(.*)$ index.php?route=$1 [L,QSA]' . "\n";

				if(!file_put_contents($xmlObj->main->root->pub . '.htaccess', $htaccess)) {
					throw new Exception('Error: .htaccess file could not be written!');
				}
			}

			// Install: Class Hierarchy
			self::out('Building namespace "' . self::getValue('app_namespace') . '"...');
			if(!is_dir(self::getValue('root_lib') . self::getValue('app_namespace'))) {
				if(!mkdir(self::getValue('root_lib') . self::getValue('app_namespace'))) {
					throw new Exception('Could not create the application library directory.');
				}
			}

			self::out('- ' . self::getValue('app_namespace') . '::Control');
			if(false === file_put_contents(self::getValue('root_lib') . self::getValue('app_namespace') . DIRECTORY_SEPARATOR . 'Control.php', self::getScriptContent('controller', self::getValue('app_namespace')))) {
				throw new Exception('Creation of base Controller failed!');
			}

			self::out('- ' . self::getValue('app_namespace') . '::Control::Index');
			if(!is_dir(self::getValue('root_lib') . self::getValue('app_namespace') . DIRECTORY_SEPARATOR . 'Control')) {
				if(!mkdir(self::getValue('root_lib') . self::getValue('app_namespace') . DIRECTORY_SEPARATOR . 'Control')) {
					throw new Exception('Could not create the application Controller root directory.');
				}
			}

			if(false === file_put_contents(self::getValue('root_lib') . self::getValue('app_namespace') . DIRECTORY_SEPARATOR . 'Control' . DIRECTORY_SEPARATOR . 'Index.php', self::getScriptContent('controller:index', self::getValue('app_namespace')))) {
				throw new Exception('Creation of index Controller failed!');
			}

			self::out('- ' . self::getValue('app_namespace') . '::View');
			if(false === file_put_contents(self::getValue('root_lib') . self::getValue('app_namespace') . DIRECTORY_SEPARATOR . 'View.php', self::getScriptContent('view', self::getValue('app_namespace')))) {
				throw new Exception('Creation of base View failed!');
			}

			self::out('- ' . self::getValue('app_namespace') . '::View::Index');
			if(!is_dir(self::getValue('root_lib') . self::getValue('app_namespace') . DIRECTORY_SEPARATOR . 'View')) {
				if(!mkdir(self::getValue('root_lib') . self::getValue('app_namespace') . DIRECTORY_SEPARATOR . 'View')) {
					throw new Exception('Could not create the application View root directory.');
				}
			}

			if(false === file_put_contents(self::getValue('root_lib') . self::getValue('app_namespace') . DIRECTORY_SEPARATOR . 'View' . DIRECTORY_SEPARATOR . 'Index.php', self::getScriptContent('view:index', self::getValue('app_namespace')))) {
				throw new Exception('Creation of index View failed!');
			}

			self::out('Installation Complete!', 'success');

			return true;
		}
		catch(Exception $ex) {
			self::out($ex->getMessage(), 'error');
			self::out('Installation Failed!', 'end');

			if(is_file($xmlObj->main->root->config . 'application.xml')) {
				@unlink($xmlObj->main->root->config . 'application.xml');
			}
			return false;
		}
	}

	/**
	 * Outputs the specified string to the browser (if output is enabled).
	 *
	 * @param string $string the string to output
	 * @param string $type the output type
	 */
	public static function out($string = '', $type = 'info') {
		if(self::$_output) {
			usleep(100000);
			
			switch(self::$_outputFormat) {
				default:
				case 'HTML':
					switch($type) {
						default:
						case 'info':
							echo '<span class="info">' . $string . '</span><br />';
							break;

						case 'success':
							echo '<span class="success">' . $string . '</span><br />';
							break;

						case 'error':
							echo '<span class="error">' . $string . '</span><br />';
							break;

						case 'warn':
							echo '<span class="warn">' . $string . '</span><br />';
							break;

						case 'status':
							echo '<span class="status">' . $string . '</span><br />';
							break;
					}

					echo '<script type="text/javascript">$.scrollTo(\'max\');</script>';
					break;

				case 'text':
					echo $string . "\n";
					break;
				
				case 'JSON':
					switch($type) {
						default:
						case 'info':
							echo '{type: "info", msg: "' . $string . '"},';
							break;

						case 'success':
							echo '{type: "success", msg: "' . $string . '"},';
							break;

						case 'error':
							echo '{type: "error", msg: "' . $string . '"},';
							break;

						case 'warn':
							echo '{type: "warn", msg: "' . $string . '"},';
							break;

						case 'status':
							echo '{type: "status", msg: "' . $string . '"},';
							break;
					}
					break;
			}

			echo str_pad('', 1024);  // minimum start for Safari
			ob_flush();
			flush();
		}
	}

	/**
	 *
	 * @param string $script the type of script to generate
	 * @param string $namespace the namespace to use
	 * @return string the assembled script content
	 */
	protected static function getScriptContent($script, $namespace = 'Application') {
		// Setup
		$result = '';
		$n = "\n";
		$t = "\t";

		switch($script) {
			case 'controller':
				$result .=
					'<?php' . $n .
                    'namespace ' . $namespace . ';' . $n .
                    $n .
					'/**' . $n .
					' * Autogenerated controller class.' . $n .
					' * ' . $n .
					' * @package ' . $namespace . $n .
					' * @author Bedrock Framework Installer' . $n .
					' * @version 1.0.0' . $n .
					' * @created ' . date('m/d/Y') . $n .
					' * @updated ' . date('m/d/Y') . $n .
					' */' . $n .
					'abstract class Control extends \\Bedrock\\Control {' . $n .
					$t . '' . $n .
					'}' . $n .
					'?>';
				break;

			case 'controller:index':
				$result .=
					'<?php' . $n .
                    'namespace ' . $namespace . '\\Control;' . $n .
                    $n .
					'/**' . $n .
					' * Autogenerated controller class.' . $n .
					' * ' . $n .
					' * @package ' . $namespace . $n .
					' * @author Bedrock Framework Installer' . $n .
					' * @version 1.0.0' . $n .
					' * @created ' . date('m/d/Y') . $n .
					' * @updated ' . date('m/d/Y') . $n .
					' */' . $n .
					'class Index extends \\' . $namespace . '\\Control {' . $n .
					$t . '/**' . $n .
					$t . ' * The main index for the controller.' . $n .
					$t . ' * ' . $n .
					$t . ' * @param array $args an array of arguments passed from the GET string' . $n .
					$t . ' * @return null' . $n .
					$t . ' */' . $n .
					$t . 'public function index($args) {' . $n .
					$t . $t . 'try {' . $n .
					$t . $t . $t . '// Sample Output (feel free to delete this)' . $n .
					$t . $t . $t . '$view = new \\' . $namespace . '\\View\\Index();' .  $n .
					$t . $t . $t . '$view->setValue(\'message\', \'Bedrock Framework Application: Installation Successful!\');' . $n .
					$t . $t . $t . '$view->render(\'test\');' . $n .
					$t . $t . '}' . $n .
					$t . $t . 'catch(\\Exception $ex) {' . $n .
					$t . $t . $t . '\\Bedrock\\Common\\Logger::exception($ex);' . $n .
					$t . $t . '}' . $n .
					$t . '}' . $n .
					'}' . $n .
					'?>';
				break;

			case 'view':
				$result .=
					'<?php' . $n .
                    'namespace ' . $namespace . ';' . $n .
                    $n .
                    '/**' . $n .
					' * Autogenerated view class.' . $n .
					' * ' . $n .
					' * @package ' . $namespace . $n .
					' * @author Bedrock Framework Installer' . $n .
					' * @version 1.0.0' . $n .
					' * @created ' . date('m/d/Y') . $n .
					' * @updated ' . date('m/d/Y') . $n .
					' */' . $n .
					'abstract class View extends \\Bedrock\\View {' . $n .
					$t . '' . $n .
					'}' . $n .
					'?>';
				break;

			case 'view:index':
				$result .=
					'<?php' . $n .
                    'namespace ' . $namespace . '\\View;' . $n .
                    $n .
                    '/**' . $n .
					' * Autogenerated view class.' . $n .
					' * ' . $n .
					' * @package ' . $namespace . $n .
					' * @author Bedrock Framework Installer' . $n .
					' * @version 1.0.0' . $n .
					' * @created ' . date('m/d/Y') . $n .
					' * @updated ' . date('m/d/Y') . $n .
					' */' . $n .
					'class Index extends \\' . $namespace . '\\View {' . $n .
					$t . '/**' . $n .
					$t . ' * Renders the page.' . $n .
					$t . ' * ' . $n .
					$t . ' * @param string $page the name of the page to render, or \'self\' for the main page' . $n .
					$t . ' * @throws \\Bedrock\\View\\Exception' . $n .
					$t . ' * @return void' . $n .
					$t . ' */' . $n .
					$t . 'public function render($page = \'self\') {' . $n .
					$t . $t . 'try {' . $n .
					$t . $t . $t . 'switch($page) {' . $n .
					$t . $t . $t . $t . '// This "test" case can be removed, it is only used once following installation.' . $n .
					$t . $t . $t . $t . 'case \'test\':' . $n .
					$t . $t . $t . $t . $t . 'echo $this->getValue(\'message\');' . $n .
					$t . $t . $t . $t . $t . 'break;' . $n .
					$t . $t . $t . $t . '' . $n .
					$t . $t . $t . $t . 'case \'self\':' . $n .
					$t . $t . $t . $t . $t . 'include $this->_root . \'index.php\';' . $n .
					$t . $t . $t . $t . $t . 'break;' . $n .
					$t . $t . $t . $t . '' . $n .
					$t . $t . $t . $t . 'case \'body\':' . $n .
					$t . $t . $t . $t . $t . 'if(count($this->_body) > 0) {' . $n .
					$t . $t . $t . $t . $t . $t . 'include $this->_root . array_pop($this->_body);' . $n .
					$t . $t . $t . $t . $t . '}' . $n .
					$t . $t . $t . $t . $t . 'break;' . $n .
					$t . $t . $t . $t . '' . $n .
					$t . $t . $t . $t . 'case \'javascript\':' . $n .
					$t . $t . $t . $t . $t . 'foreach($this->_javascript as $javascript) {' . $n .
					$t . $t . $t . $t . $t . $t . 'include $javascript;' . $n .
					$t . $t . $t . $t . $t . '}' . $n .
					$t . $t . $t . $t . $t . $n .
					$t . $t . $t . $t . $t . 'break;' . $n .
					$t . $t . $t . $t . '' . $n .
					$t . $t . $t . $t . 'default:' . $n .
					$t . $t . $t . $t . $t . 'break;' . $n .
					$t . $t . $t . '}' . $n .
					$t . $t . '}' . $n .
					$t . $t . 'catch(\\Exception $ex) {' . $n .
					$t . $t . $t . '\\Bedrock\\Common\\Logger::exception($ex);' . $n .
					$t . $t . $t . 'throw new \\Bedrock\\View\\Exception(\'The view could not be rendered.\');' . $n .
					$t . $t . '}' . $n .
					$t . '}' . $n .
					'}' . $n .
					'?>';
				break;
		}

		return $result;
	}

	/**
	 * Compares the current system requirement setting with the available value.
	 *
	 * @param string $name the name of the system requirement to check
	 * @return boolean whether or not the value meets or surpasses the currently set requirement
	 */
	public static function checkReq($name) {
		// Setup
		$result = false;
		
		switch($name) {
			case 'php':
				if(version_compare(PHP_VERSION, self::$_requirements['php'], '>=')) {
					$result = true;
				}
				break;

			case 'os':
				$result = in_array(PHP_OS, self::$_requirements['os']);
				break;

			case 'bedrock':
				$json = file_get_contents('http://www.bedrockframework.com/ajax/');
				$data = json_decode($json);
				
				$result = version_compare($data->version->stable, '0.1.0', '>=');
				
				break;

			case 'filesystem':
				$result = is_writeable(realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR));
				break;
		}

		return $result;
	}

	/**
	 * Retrieves the proper IMG tag for the specified icon type.
	 *
	 * @param string $type the icon type to retrieve
	 * @return string the corresponding IMG tag
	 */
	public static function getIcon($type) {
		// Setup
		$result = '';

		switch($type) {
			case 'checking':
				$result = '<img src="images/dot_gray.png" width="16" height="16" alt="Checking..." />';
				break;

			case 'success':
				$result = '<img src="images/dot_green.png" width="16" height="16" alt="Success" />';
				break;

			case 'error':
				$result = '<img src="images/dot_red.png" width="16" height="16" alt="Error" />';
				break;

			case 'info':
				$result = '<img src="images/dot_blue.png" width="16" height="16" alt="Info" />';
				break;

			case 'warn':
				$result = '<img src="images/dot_yellow.png" width="16" height="16" alt="Warning" />';
				break;
		}

		return $result;
	}

	/**
	 * Processes a submitted form for the installation process.
	 * 
	 * @return boolean whether or not the submission was processed
	 */
	public static function process() {
		// Setup
		$result = false;
		$step = $_POST['step'];

		switch($step) {
			default:
				$result = false;
				break;

			case 'start':
				$result = true;
				break;

			case 1:
				// Validate: Application Name
				if(trim($_POST['app_name']) != '') {
					self::setValue('app_name', trim($_POST['app_name']));
				}
				else {
					self::error(1, 'app_name', 'You must specify a name for your application.');
				}

				// Validate: Application Version
				if(trim($_POST['app_version']) != '') {
					self::setValue('app_version', trim($_POST['app_version']));
				}

				// Validate: Application Namespace
				if(trim($_POST['app_namespace']) != '') {
					self::setValue('app_namespace', trim($_POST['app_namespace']));

					if(self::getValue('database_prefix') == '') {
						self::setValue('database_prefix', strtolower(trim($_POST['app_namespace'])) . '_');
					}
				}
				else {
					self::error(1, 'app_namespace', 'A namespace for your application is required.');
				}

				if(!self::hasErrors(1)) {
					$result = true;
					self::$_steps['step_01'] = true;
				}
				break;

			case 2:
				// Validate: Directory Settings
				if($_POST['root'] == 'custom') {
					self::setValue('root', 'custom');

					// Validate: Application Root
					if(is_dir($_POST['root_system'])) {
						if(substr($_POST['root_system'], -1) != DIRECTORY_SEPARATOR) {
							$_POST['root_system'] .= DIRECTORY_SEPARATOR;
						}
						
						self::setValue('root_system', $_POST['root_system']);
					}
					else {
						self::error(2, 'root_system', 'Directory not found, please enter a valid path.');
					}

					// Validate: Log Directory
					if(!is_dir($_POST['root_log'])) {
						self::error(2, 'root_log', 'Directory not found, please enter a valid path.');
					}
					elseif(!is_writeable($_POST['root_log'])) {
						self::error(2, 'root_log', 'Directory is not writeable.');
					}
					else {
						if(substr($_POST['root_log'], -1) != DIRECTORY_SEPARATOR) {
							$_POST['root_log'] .= DIRECTORY_SEPARATOR;
						}

						self::setValue('root_log', $_POST['root_log']);
					}

					// Validate: Public Root
					if(is_dir($_POST['root_pub'])) {
						if(substr($_POST['root_pub'], -1) != DIRECTORY_SEPARATOR) {
							$_POST['root_pub'] .= DIRECTORY_SEPARATOR;
						}

						self::setValue('root_pub', $_POST['root_pub']);
					}
					else {
						self::error(2, 'root_pub', 'Directory not found, please enter a valid path.');
					}
				}
				elseif($_POST['root'] == 'default') {
					self::setValue('root', 'default');
				}
				else {
					self::error(2, 'root', 'Please select whether to use the default or custom directory settings.');
				}

				if(!self::hasErrors(2)) {
					$result = true;
					self::$_steps['step_02'] = true;
				}
				break;

			case 3:
				if($_POST['database_type'] == 'mysql_5') {
					self::setValue('database_type', 'mysql_5');

					if(trim($_POST['database_address']) == '') {
						self::error(3, 'database_address', 'Please enter a valid address.');
					}
					else {
						self::setValue('database_address', trim($_POST['database_address']));
					}

					if(trim($_POST['database_name']) == '') {
						self::error(3, 'database_name', 'Please enter a valid database name.');
					}
					else {
						self::setValue('database_name', trim($_POST['database_name']));
					}

					if(trim($_POST['database_username']) == '') {
						self::error(3, 'database_username', 'A valid username is required.');
					}
					else {
						self::setValue('database_username', trim($_POST['database_username']));
					}

					if(trim($_POST['database_password']) == '') {
						self::error(3, 'database_password', 'A valid password is required.');
					}

					self::setValue('database_prefix', trim($_POST['database_prefix']));

					if(!self::hasErrors(3)) {
						// Test Database Connection
						try {
							self::setValue('database_password', trim($_POST['database_password']));
							$dsn = 'mysql:host=' . self::getValue('database_address') . ';dbname=' . self::getValue('database_name');
							new PDO($dsn, self::getValue('database_username'), self::getValue('database_password'));
						}
						catch(PDOException $ex) {
							$errorCode = $ex->getMessage();
							$errorCode = strrchr($errorCode, '[');
							$errorCode = substr($errorCode, 1, 4);
							
							switch($errorCode) {
								default:
								case 1042:
								case 1043:
								case 1081:
								case 1105:
								case 1129:
								case 1130:
								case 1469:
									self::error(3, 'database_address', 'A connection to the database could not be made (' . $errorCode . ').');
									break;

								case 1044:
								case 1045:
								case 1396:
								case 1449:
								case 1468:
									self::error(3, 'database_username', 'Access was denied for this user.');
									break;

								case 1133:
								case 1275:
									self::error(3, 'database_password', 'The specified password was not accepted.');
									break;

								case 1046:
								case 1049:
								case 1102:
									self::error(3, 'database_name', 'The specified database could not be found.');
									break;
							}
						}
					}
				}
				elseif($_POST['database_type'] == 'none') {
					self::setValue('database_type', 'none');
				}
				else {
					self::error(3, 'database_type', 'You must select a database type to continue.');
				}

				if(!self::hasErrors(3)) {
					$result = true;
					self::$_steps['step_03'] = true;
				}
				break;

			case 4:
				// Validate: Friendly URLs
				if($_POST['friendly'] == 'yes') {
					self::setValue('friendly', true);
				}
				elseif($_POST['friendly'] == 'no') {
					self::setValue('friendly', false);
				}
				else {
					self::error(4, 'friendly', 'You must opt to either use "friendly" URLs or not.');
				}

				// Validate: Timezone
				self::setValue('timezone', $_POST['timezone']);

				// Validate: Logging
				$logTo = array();

				foreach($_POST['logto'] as $log) {
					if($log == 'system' || $log == 'file' || $log == 'firephp' || $log == 'growl') {
						$logTo[] = $log;
					}
				}

				self::setValue('logto', $logTo);

				// Validate: Growl
				if(trim($_POST['growl_address']) != '') {
					self::setValue('growl_address', trim($_POST['growl_address']));
				}
				elseif(in_array('growl', $logTo)) {
					self::error(4, 'growl_address', 'If you choose to log to a Growl client, you must specify its address.');
				}
				
				if(!self::hasErrors(4)) {
					$result = true;
					self::$_steps['step_04'] = true;
				}
				break;

			case 'install':
				header('Location: ../');
				break;
		}

		return $result;
	}

	/**
	 * Stores an error message for the specified field.
	 *
	 * @param integer $step the step in the form
	 * @param string $field the affected field
	 * @param string $message the error message to use
	 */
	public static function error($step, $field, $message) {
		self::$_errors[$step][$field] = $message;
	}

	/**
	 * Checks if any errors are currently stored.
	 * 
	 * @param integer $step the step in the form
	 * @param string $field the field to check
	 * @return boolean whether or not any errors were found
	 */
	public static function hasErrors($step = 0, $field = '') {
		// Setup
		$result = false;

		if(count(self::getErrors($step, $field)) > 0) {
			$result = true;
		}
		
		return $result;
	}

	/**
	 * Retrieves the requested errors.
	 *
	 * @param integer $step the step in the form
	 * @param string $field the field to search for
	 * @return mixed the requested errors (if any)
	 */
	public static function getErrors($step = 0, $field = '') {
		// Setup
		$result = array();

		if(is_numeric($step) && $step > 0 && array_key_exists($step, self::$_errors)) {
			if(trim($field) != '' && array_key_exists($field, self::$_errors[$step])) {
				$result = self::$_errors[$step][$field];
			}
			else {
				$result = self::$_errors[$step];
			}
		}
		elseif($step == 0) {
			$result = self::$_errors;
		}

		return $result;
	}

	/**
	 * Clears all currently stored errors.
	 */
	public static function clearErrors() {
		self::$_errors = array();
	}

	/**
	 * Sets the specified value.
	 * 
	 * @param string $name the name of the value to set
	 * @param mixed $value the value to apply
	 * @param mixed $default the default value to use
	 */
	public static function setValue($name, $value, $default = null) {
		if($value != '') {
			self::$_data[$name] = $value;
		}
		else {
			self::$_data[$name] = $default;
		}
	}

	/**
	 * Retrieves the specified value.
	 *
	 * @param string $name the name of the value to retrieve
	 * @return mixed the corresponding value
	 */
	public static function getValue($name) {
		if(array_key_exists($name, self::$_data)) {
			return self::$_data[$name];
		}
		else {
			return null;
		}
	}

	/**
	 * Outputs the specified value.
	 *
	 * @param string $name the name of the value to output
	 */
	public static function printValue($name) {
		echo self::getValue($name);
	}

    /**
     * Recursively deletes the contents of the specified directory.
     *
     * @param string $directory the path to recursively delete
     *
     * @throws Exception when there is a problem with the specified directory
     * @return void
     */
	protected static function removeDirectoryRecursive($directory) {
		// Clean Trailing Directory Separator
		if(substr($directory, -1) == DIRECTORY_SEPARATOR) {
			$directory = substr($directory, 0, -1);
		}

		// Verify Directory is Valid
		if(!file_exists($directory) || !is_dir($directory)) {
			throw new Exception('Invalid directory specified.');
		}
		elseif(!is_readable($directory)) {
			throw new Exception('The specified directory is not readable.');
		}

		// Access Directory
		$handle = opendir($directory);

		while(false !== ($item = readdir($handle))) {
			if($item != '.' && $item != '..') {
				$path = $directory . DIRECTORY_SEPARATOR . $item;

                if(is_dir($path)) {
                    self::removeDirectoryRecursive($path);
                }
                else {
                    unlink($path);
                }
            }
		}

		closedir($handle);

		if(rmdir($directory)) {
			throw new Exception('The directory "' . $directory . '" could not be removed.');
		}
	}

	/**
	 * Outputs the nav button HTML.
	 *
	 * @param integer $step the step number to use
	 * @param boolean $current whether or not the step is the current one
	 */
	public static function printNavButton($step, $current = false) {
		// Setup
		$html = '<div class="step disabled"></div>';
		$padded = str_pad($step, 2, '0', STR_PAD_LEFT);

		if(self::$_steps['step_' . $padded] || $current) {
			$html = '<input type="button" id="goto_' . $padded . '" class="step' . ($current ? ' current' : '') . '" name="" value="" />';
		}
		
		echo $html;
	}
}