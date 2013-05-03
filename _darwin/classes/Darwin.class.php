<?php
class Darwin {
	private $config;
	private $router;
	
	public function __construct($config) {
		spl_autoload_register('Darwin::classLoader');
		
		$this->config = new DarwinConfig($config);

		$this->defineRoutes();
		 
		// $this->setConstants();
	}

	/**
	 * Autoload classes -very basic, only loads classes in root of classes folder
	 * @param  string $class_name
	 * @return void
	 */
	public static function classLoader($class_name) {
		include __DIR__ .'/'. $class_name .'.class.php';
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
		$this->router = new Router();

		// Add Darwins default routes
		$this->router->add('/', 'RootAction');
		$this->router->add('/darwin', 'DarwinAction');
		
		// Loop over and add custom routes
		if (isset($this->config->routes)) {
			foreach ($this->config->routes as $uri => $action) {
				$this->router->add($uri, $action);
			}
		}
	}

	/**
	 * Set constants in order to be backwards compatible with previous Darwin versions
	 */
	private function setConstants() {

	}
}