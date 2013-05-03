<?php
/**
 * Starting point of all requests
 * You may want to change the location of the darwin folder, to have a single instance
 * shared by several projects. Then change the require_once() call.
 * @requires PHP version 5.3
 */
define('APP_ROOT', __DIR__);
require_once(APP_ROOT .'/_darwin/darwin.php');