<?php
namespace Bedrock\Common;

/**
 * Router
 *
 * The main router object that handles requests and loads the proper controller.
 *
 * @package Bedrock
 * @author Nick Williams
 * @version 1.1.0
 * @created 02/30/2008
 * @updated 07/02/2012
 */
class Router extends \Bedrock {
	protected $_args = array();
	protected $_delegationQueue = array();

	/**
	 * Initializes the router, establishing protected config and logger objects.
	 *
	 * @param string $delegationQueue a list of root controller classes in order of precedence for delegation
	 */
	public function __construct($delegationQueue = array()) {
		if(count($delegationQueue) == 0) {
			$this->defaultDelegationQueue();
		}
		else {
			foreach($delegationQueue as $rootController) {
				$this->addToDelegationQueue($rootController);
			}
		}

		parent::__construct();
	}

	/**
	 * Adds a root controller to the delegation queue.
	 *
	 * @param array $rootController an array containing the root controller's path and class name
	 */
	public function addToDelegationQueue($rootController) {
		if(is_array($rootController) && array_key_exists('path', $rootController) && array_key_exists('class', $rootController)) {
			$this->_delegationQueue[] = array('class' => $rootController['class'], 'path' => $rootController['path']);
		}
	}

	/**
	 * Applies the default delegation queue to the router, setting the router to
	 * first search for a controller within the application's namespace, and
	 * within Bedrock's controller classes.
	 */
	public function defaultDelegationQueue() {
		$config = \Bedrock\Common\Registry::get('config');

        // TODO: Update to properly handle namespace refactored code.
		$this->addToDelegationQueue(array(
				'class' => $config->meta->namespace . '\\Control',
				'path' => $config->root->lib . $config->meta->namespace . DIRECTORY_SEPARATOR . 'Control' . DIRECTORY_SEPARATOR
		));

		$this->addToDelegationQueue(array(
				'class' => 'Bedrock\\Control',
				'path' => $config->root->lib . 'Bedrock' . DIRECTORY_SEPARATOR . 'Control' . DIRECTORY_SEPARATOR
		));
	}

	/**
	 * Delegates the request to the proper controller.
	 */
	public function delegate() {
		try {
			// Setup
			$route = '';
			$parts = array();
			$args = array();

			// Find Route
			$route = (empty($_GET['route'])) ? '' : $_GET['route'];

			if(empty($route)) {
                $route = "index";
            }

			\Bedrock\Common\Logger::info('Route: ' . $route);

			// Separate the route into parts.
			$route = trim($route, "/\\");
			$parts = explode("/", $route);

			// Find Controller
			if($parts[0] == 'query' || substr($parts[0], 0, 6) == 'query:') {
            	$controller = new \Bedrock\Control\Query();
            	$controller->index($parts);
            }
			else {
				// Handle Stored Special Cases
            	$cases = \Bedrock\Common\Router\SpecialCases::retrieve();
				$specialCase = null;

            	foreach($cases as $case) {
            		if(substr($route, 0, strlen($case['route'])) == $case['route']) {
            			$controller = new $case['controller']();
            			$controller->{$case['method']}($parts);

            			$specialCase = true;
            			break;
            		}
            	}

				if(!$specialCase) {
					$parts = array_map('ucwords', $parts);
					$controller = null;

					while($parts) {
						// Check for ::index() method.
						array_push($parts, 'index');
						$controller = self::getController($this->_delegationQueue, $parts);
						if($controller) break;

						// Check for specific method.
						array_pop($parts);
						$controller = self::getController($this->_delegationQueue, $parts);
						if($controller) break;

						// Otherwise continue loop.
						array_unshift($args, strtolower(array_shift($parts)));
					}

					$action = strtolower(array_pop($parts));

					// Finally, delegate to a 404 error.
					if($controller === false) {
						\Bedrock\Common\Logger::error('No controller found using route: "' . $route . '"');
						$this->delegateToError();
					}
					elseif(!method_exists($controller, $action)) {
						\Bedrock\Common\Logger::error('No action found using route: "' . $route . '"');
						$this->delegateToError();
					}
					else {
						// Execute Controller Method
						$controller->$action($parts);
					}
				}
			}

		}
		catch(\Bedrock\Common\Router\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			$this->delegateToError();
		}
	}

	/**
	 * Attempts to instantiate a controller using the specified parameters.
	 *
	 * @param array $delegationQueue an array of root controllers to use, in order of precedence
	 * @param array $route the route to use
	 * @return mixed either the controller if it exists, or false otherwise
	 */
	protected static function getController($delegationQueue, $route) {
		try {
			// Setup
			$result = false;

			foreach($delegationQueue as $rootController) {
				$method = strtolower(array_pop($route));
				$file = array_pop($route);
				$path = $rootController['path'] . implode(DIRECTORY_SEPARATOR, $route) . DIRECTORY_SEPARATOR;
				$fullpath = $path . $file . '.php';
				$class = $rootController['class'] . (count($route) > 0 ? '\\' . implode('\\', $route) : '') . '\\' . $file;

				if(is_file($fullpath) && is_readable($fullpath) && is_callable($class, $method)) {
					$result = new $class();
					break;
				}
			}

			return $result;
		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Router\Exception($ex->getMessage());
		}
	}

	/**
	 * Delegates the request to an error dialog.
	 */
	private function delegateToError() {
		try {
			// Setup
			$controller = false;

			foreach($this->_delegationQueue as $rootController) {
				$path = $rootController['path'] . DIRECTORY_SEPARATOR . 'Error.php';

				if(is_file($path) && is_readable($path)) {
					$class = $rootController['class'] . '\\Error';

					if(method_exists($class, 'error')) {
						$controller = new $class();
						break;
					}
				}
			}

			header($_SERVER["SERVER_PROTOCOL"] . 'Not Found', true, 404);

			if($controller !== false) {
				$controller->type = 'HTTP404';
				$controller->error();
			}

		}
		catch(\Exception $ex) {
			\Bedrock\Common\Logger::exception($ex);
			throw new \Bedrock\Common\Router\Exception($ex->getMessage());
		}
	}
}