<?php
/**
 * This is the autoload class
 * Help to load appropriated php file for className
 */
class Autoloader
{
    static public function registerAutoload()
    {
        spl_autoload_register(array('Autoloader', 'loadClass'));
    }
    static public function loadClass($className)
    {
        $className = str_replace('\\', '/', $className);
        @include $className.'.php';
    }
}