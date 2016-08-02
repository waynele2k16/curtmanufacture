<?php
class View
{
    protected $_vars = array();
    
    protected $_template = null;
    
    protected function _init()
    {
        if ($this->_template === null) {
            $view = App::registry('action').'.phtml';
            $view = App::registry('controller') . DS . $view;
            $this->_template = $view;
        } else {
            $this->_template = $this->_template;
        }
    }
    
    public function setVars($vars)
    {
        $this->_vars = $vars;
    }
    
    public function setVar($key, $value)
    {
        $this->_vars[$key] = $value;
    }
    
    public function getVars()
    {
        return $this->_vars;
    }
    
    public function render($view = null, $vars = array())
    {
        if ($view !== null) {
            $this->_template = $view;
        }
        
        if (!empty($vars)) {
            $this->_vars = array_merge($this->_vars, $vars);
        }
        
        $this->_init();
        
        try{
            extract($this->getVars());
            include APP_VIEW_PATH . DS . $this->_template;
        } catch (Exception $e) {
            throw $e;
        }
    }
}