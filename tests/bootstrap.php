<?php
/**
 * @author    Wayne Le
 */
define('APP_ROOT', getcwd());
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
include (__DIR__ . '/../classes/App.php');
App::run();
echo "System loaded. Running Tests\n";