<?php
/**
 * Validates the passed config array and makes all properties publicly accessible
 * @requires APP_ROOT constant
 */
class DarwinConfig {
	public $legacy = false;
	public $template_dir = 'templates/';
	public $homepage = '';
	public $routes = array();
	
	public function __construct($config) {
		foreach ($config as $key => $value) {
			if (property_exists($this, $key) && $this->check($key, $value)) {
				$this->$key = $value;
			}
		}
		
		if (Log::count('error')) {
			echo 'Error while configuring. Full log:<br />';
			Log::output();
			die();
		}
	}
	
	/**
	 * Forms a method name from the passed property in the format "check<PropertyName>" and
	 * calls it for effect.
	 * @param string $key
	 * @param string $value
	 * @return bool
	 */
	protected function check($key, $value) {
		$method = 'check'. ucfirst($key);
		$method = preg_replace('/_([a-zA-Z])/e', "strtoupper('$1')", $method);
		return $this->{$method}($value);
	}
	
	protected function checkLegacy($value) {
		$ok = !is_bool($value);
		if (!$ok) {
			Log::warn('Legacy value should be boolean; Legacy is now off (default)', 'config.php');
		}
		return $ok;
	}
	
	protected function checkTemplateDir($value) {
		$ok = true;
		if (!is_dir(APP_ROOT . $value)) {
			$ok = false;
			Log::warn("'template_dir' ($value) not found; Trying to revert to default: '/templates'", 'config.php');
		}
		
		if (substr($value, 0, 1) !== '/') {
			$ok = false;
			Log::error("'template_dir' should start with a slash (/); Please update config.php", 'config.php');
		}
		return $ok;
	}
	
	/**
	 * @TODO change to is_file or check if it's a valid route/uri
	 */
	protected function checkHomepage($value) {
		$ok = is_string($value);
		if (!$ok) {
			Log::warn('Homepage is not correct; Template index will be used', 'config.php');
		}
		return $ok;
	}
	
	/**
	 * Thoroughly check the custom routing rules
	 * A valid route is: array('/<uri>' => '<nameOf>Action')
	 * TODO: test test test
	 * @param array $value
	 * @return boolean
	 */
	protected function checkRoutes($value) {
		$ok = true;
		if (is_array($value)) {
			foreach ($value as $uri => $action) {
				// Check if uri is valid string
				if (!is_string($uri) || strpos($uri, '/') !== 0) {
					$ok = false;
					Log::error("Not a valid uri for route (should start with /): '$uri'; route ignored", 'config.php');
					break; // breaking to show 1 error at a time
				}
				
				// Check for reserved uri's
				if ($uri === '/' || $uri === '/darwin') {
					Log::warn("Custom route is overwriting default: '$uri' will be overwritten by '$action'", 'config.php');
				}
				
				// Check if the connected Action exists
				if (!class_exists($action)) {
					$ok = false;
					Log::error("Action not found for: '$uri'; route ignored", 'config.php');
					break;
				}
			}
		}
		return $ok;
	}
}