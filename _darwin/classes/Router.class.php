<?php
class Router {
	
	private $controller_dirs = array();
	private $rules = array();
	
	private $controller = null;
	private $action = null;
	private $params = array();

	const default_controller = 'Default';
	const default_action = 'Default';
	
	public function __construct($controller_dirs) {
		if (is_string($controller_dirs)) {
			$controller_dirs = array($controller_dirs);
		}
		$this->controller_dirs = $controller_dirs;
	}

	/**
	 * Add a route rule if you want to use a different path then will be
	 * translated from the uri
	 * eg. /obfuscated/url => /realcontroller/action
	 * @param string $uri
	 * @param string $route 
	 * @param bool $suppress_overwrite_warning
	 */
	public function addRule($uri, $route, $suppress_overwrite_warning = false) {
		if ($this->controller) {
			Log::error("Cannot add rule '$uri' => '$route' because the router has already started.", 1);
			return false;
		}
		
		// Log a warning if rule already exists 
		if (isset($this->rules[$uri]) && !$suppress_overwrite_warning) {
			Log::warn("Overwriting route rule: '$uri' => '$action' (old route: '{$this->routes[$id]['route']}')");
		} else {
			Log::info("Adding rule '$uri' => '$route'");
		}
		$this->rules[trim($uri, '/ ')] = trim($route, '/ ');
	}
	
	/**
	 * Processes the request
	 * Note that the routes array is reversed, so the request falls back to root (/)
	 * TODO: implement custom rules
	 */
	public function run() {
		$request_uri = isset($_GET['uri'])? trim($_GET['uri'], '/ ') : '';
		
		// Alter request uri if a rule is found
		if (isset($this->rules[$request_uri])) {
			Log::info("Request intercepted by custom rule: '$request_uri' => '{$this->rules[$request_uri]}'");
			$request_uri = $this->rules[$request_uri];
		}
		
		$request = $request_uri? explode('/', $request_uri) : array();
		
		$this->initialiseRequest($request);
		$this->runController();
		
		
		
//			include_once (APP_ROOT .'/'. $this->config->template_dir .'/'. $_GET['uri']);
//		} else {
//			if ($this->config->homepage !== '') {
//				include_once (APP_ROOT .'/'. $this->config->template_dir .'/'. $this->config->homepage);
//			} else {
//				// TODO -darwin rewrite
//				// header('Location:'. $_SERVER['PHP_SELF'] .'/darwin/list.php');
//				die('Go to template index');
//			}
//		}
	}
	
	/**
	 * Initialise the controller/action call
	 * @param array $request
	 */
	protected function initialiseRequest($request = array()) {
		if (count($request) === 0) {
			$this->controller = self::default_controller .'Controller';
			$this->action = self::default_action .'Action';
		} else {
			$this->controller = ucfirst(strtolower($request[0])) .'Controller';
			
			if (isset($request[1])) {
				$this->action = ucfirst(strtolower($request[1])) .'Action';
			} else {
				$this->action = self::default_action .'Action';
			}
			
			if (count($request) > 2) {
				$this->params = array_slice($request, 2);
			}
		}
	}
	
	protected function runController() {
		$controller = null;
		
		foreach ($this->controller_dirs as $dir) {
			echo $dir . $this->controller .'.class.php' .'<br />';
			if (is_file($dir . $this->controller .'.class.php')) {
				$controller = new $this->controller($this->params);
				break;
			}
		}
		
		if ($controller === null) {
			Log::error("Can't find controller {$this->controller}");
			$this->initialiseRequest(); // Initialise with default settings
		}
	}
}