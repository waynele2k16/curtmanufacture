<?php
/**
 * Prepare include paths for the application
 */
App::register('original_include_path', get_include_path());
define('APP_CONTROLLER_PATH', APP_ROOT . DS . 'app' . DS . 'controllers');
define('APP_VIEW_PATH', APP_ROOT . DS . 'app' . DS . 'views');
define('APP_MODEL_PATH', APP_ROOT . DS . 'app' . DS . 'Model');
define('APP_ZEND_PATH', APP_ROOT . DS . 'classes' . DS . 'http');

$paths[] = APP_CONTROLLER_PATH;
$paths[] = APP_VIEW_PATH;
$paths[] = APP_MODEL_PATH;

$appPath = implode(PS, $paths);
set_include_path($appPath . PS . App::registry('original_include_path'));

require_once 'Autoloader.php';

/**
 * This is the main class for the application
 */
final class App
{
    static private $_registry = array();
    
    static public function bootstrap()
    {
        Autoloader::registerAutoload();
    }
    
    static public function run()
    {
        static::bootstrap();
        //Dispatch the request to appropriated controller/action
        FrontController::dispatch();
    }
    
    /**
     * Register a new variable
     * @param string $key
     * @param mixed $value
     * @param bool $graceful
     * @throws Exception
     */
    public static function register($key, $value, $graceful = false)
    {
        if (isset(static::$_registry[$key])) {
            if ($graceful) {
                return;
            }
            throw new Exception('App registry key "'.$key.'" already exists');
        }
        static::$_registry[$key] = $value;
    }
    
    /**
     * Retrieve a value from registry by a key
     * @param string $key
     * @return mixed
     */
    public static function registry($key)
    {
        if (isset(static::$_registry[$key])) {
            return static::$_registry[$key];
        }
        return null;
    }
    
    static public function getBaseUrl()
    {
        
        if(isset($_SERVER['HTTPS'])){
            $https = $_SERVER['HTTPS'];
            $protocol = ($https != "off") ? "https" : "http";
        } else {
            $protocol = 'http';
        }
        $baseUrl = $protocol . "://" . isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST']: '' ;
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];
        $basepath = str_replace($documentRoot, '', APP_ROOT);
        
        $baseUrl .= $basepath;
        return $baseUrl;
    }
    
    static public function getData($key = null)
    {
        $shopData = array();
        if(isset($_SESSION['shopdata'])) {
            $shopData = $_SESSION['shopdata'];
        }
        
        if ($key !== null && !empty($key)) {
            $value = '';
            if (isset($shopData[$key])) {
                $value = $shopData[$key];
            }
            return $value;
        }
        if (is_array($shopData)) {
            return $shopData;
        }
        return array();
    }
    
    static public function setData($key, $value=null)
    {
        if(is_array($key)) {
            $_SESSION['shopdata'] = $key;
        } else {
            $_SESSION['shopdata'][$key] = $value;
        }
    }
}