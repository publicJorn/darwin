<?php
class Darwin {
	private $config;
	private $router;
	
	private $darwin_root = '';
	private $app_root = '';
	
	public function __construct($config) {
		// Init class loader
		require_once(__DIR__ .'/ClassLoader.class.php');
		ClassLoader::$root_class_dirs = array(__DIR__);
		spl_autoload_register('ClassLoader::load');
		
		// Init the rest
		$this->config = new DarwinConfig($config);
		$this->darwin_root = __DIR__;
		$this->app_root = defined(APP_ROOT)? APP_ROOT : substr(__DIR__, 0, strrpos(__DIR__, DIRECTORY_SEPARATOR));
		
		$this->defineRoutes();
		
		// $this->setConstants();
	}

	/**
	 * Follow through with the request
	 * Separate function for more flexibility; might want to do more checks in the future
	 * @return void
	 */
	public function run() {
		$this->router->run();
	}

	/**
	 * Setup the routing system
	 * @return void
	 */
	private function defineRoutes() {
		$this->router = new Router($this->darwin_root .'/controllers/');

		// Loop over and add custom routes
//		if (isset($this->config->routes)) {
//			foreach ($this->config->routes as $uri => $action) {
//				$this->router->add($uri, $action);
//			}
//		}
	}

	/**
	 * Set constants in order to be backwards compatible with previous Darwin versions
	 */
	private function setConstants() {

	}
}