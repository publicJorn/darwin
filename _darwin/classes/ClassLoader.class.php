<?php
class ClassLoader {
	public static $root_class_dirs = array();
	private static $current_root = '';
	
	/**
	 * Autoload classes -very basic, only loads classes in root of classes folder
	 * @param  string $class_name
	 * @return void
	 */
	public static function load($class_name) {
		if (class_exists($class_name)) {
			return;
		}
		
		foreach (self::$root_class_dirs as $root) {
			self::$current_root = $root;
			$class_dir = self::classFolder($class_name);
			
			if ($class_dir !== false) {
				require_once($class_dir . $class_name .'.class.php');
				break; // out of foreach
			}
		}
	}
	
	/**
	 * Recursively search for class file through dirs
	 * @param string $class_name
	 * @param string $sub
	 * @return mixed Path of dir where class is found OR false
	 */
	public static function classFolder($class_name, $sub = '/') {
		$dir = dir(self::$current_root . $sub);
		
		if (file_exists(self::$current_root . $sub . $class_name . '.class.php')) {
			return self::$current_root . $sub;
		}

		while (($folder = $dir->read()) !== false) {
			if ($folder != '.' && $folder != '..') {
				if (is_dir(self::$current_root . $sub . $folder)) {
					$subFolder = self::classFolder($class_name, $sub . $folder . '/');

					if ($subFolder) {
						return $subFolder;
					}
				}
			}
		}
		
		$dir->close();
		return false;
	}
}