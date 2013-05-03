<?php
class Router {
	
	private $has_run = false;
	private $uris = array();
	private $actions = array();

	public function __construct() {
		
	}

	/**
	 * Add a route if not previously set
	 * @param string $uri
	 * @param string $action Class to init this request by
	 */
	public function add($uri, $action) {
		if ($this->has_run) {
			Log::error("Cannot add '$action' for '$uri' because the router has already started.", 1);
			return false;
		}
		
		$id = array_search($uri, $this->uris);
		if ($id === false) {
			Log::info("Adding '$action' for '$uri'");
			$this->uris[] = $uri;
			$this->actions[] = $action;
		} else {
			Log::warn("Overwriting action for '$uri' with '$action'; Was '{$this->actions[$id]}'");
			$this->uris[$id] = $uri;
			$this->actions[$id] = $action;
		}
	}

	public function run() {
		$this->add('/pages', 'PageAction');
		
		echo '<pre>';
		print_r($this->uris);
		
		$this->has_run = true;
		$this->uris = array_reverse($this->uris);
		
		$request_uri = isset($_GET['uri'])? '/'. $_GET['uri'] : '/';
		
		if ($request_uri !== '') {
			foreach ($this->uris as $route_uri) {
				echo '['. $request_uri .'] '. $route_uri;
				if (preg_match("#^$route_uri/?(.+)$#", $request_uri)) {
					echo ' *';
				}
				echo '<br />';
			}
		}
		
		
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
}