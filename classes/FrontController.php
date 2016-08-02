<?php
/**
 * This is the front controler class
 * Take requestion urls, find, and dispatch the request to appropriated Controller/Action
 */
class FrontController
{
    /**
     * Get request urls
     * Find which contoller/action file need to be load
     * Handle the request
     */
    static public function dispatch()
    {
        $documentRoot = $_SERVER ['DOCUMENT_ROOT'];
        $basepath = str_replace ( $documentRoot, '', APP_ROOT );
        
        $uri = isset($_SERVER ['REQUEST_URI']) ? $_SERVER ['REQUEST_URI'] : '';
        
        if (! empty ( $basepath )) {
            $uri = static::replaceFirst ( $basepath, '', $uri );
        }
        
        $uri = substr ( $uri, 1 );
        
        if (false !== ($pos = strpos ( $uri, '?' ))) {
            $uri = substr ( $uri, 0, $pos );
        }
        $parts = explode ( '/', $uri );
        
        $controller = 'index';
        $action = 'index';
        
        if (isset ( $parts [0] ) && $parts [0]) {
            $controller = $parts [0];
        }
        
        if (isset ( $parts [1] ) && $parts [1]) {
            $action = $parts [1];
        }
        App::register ( 'controller', $controller );
        $controller = ucfirst ( $controller ) . 'Controller';
        App::register ( 'action', $action );
        $action .= 'Action';
        
        if (class_exists ( $controller )) {
            $instance = new $controller ();
        } else {
            $instance = new IndexController ();
            $action = 'notFoundAction';
        }
        
        if ($instance instanceof ActionController) {
            if (! method_exists ( $instance, $action )) {
                $action = 'notFoundAction';
            }
            call_user_func ( array (
                    $instance,
                    $action 
            ) );
        }
    }
    /**
     * Replace first occurrence
     * 
     * @param string $find            
     * @param string $replace            
     * @param string $subject            
     * @return string
     */
    static public function replaceFirst($find, $replace, $subject)
    {
        // stolen from the comments at PHP.net/str_replace
        // Splits $subject into an array of 2 items by $find,
        // and then joins the array with $replace
        return implode ( $replace, explode ( $find, $subject, 2 ) );
    }
}