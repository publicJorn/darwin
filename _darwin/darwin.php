<?php
/**
 * This is the Darwin bootstrapper.
 * It loads the project configuration into the Darwin instance.
 */
require_once(APP_ROOT .'/config.php');
require_once(__DIR__ .'/classes/Darwin.class.php');
$d = new Darwin(isset($config)? $config : array());
$d->run();
unset($config);
