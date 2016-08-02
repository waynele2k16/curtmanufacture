<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('APP_ROOT', getcwd());
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
//Bootstrap and run the application
require_once APP_ROOT . '/classes/App.php';
App::run();