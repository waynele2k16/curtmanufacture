<?php
/**
 * This is the base class for application controllers
 * Other controller classes need to extend this class
 */
class ActionController
{
    protected $_view = null;
    
    public function __construct()
    {
        $this->_view = new View();
    }
    
    public function getView()
    {
        if ($this->_view === null) {
            $this->_view = new View();
        }
        return $this->_view;
    }
    
    /**
     * Find and render view for Controller/Action
     * @param string $view
     */
    public function renderView($view = null, $vars = array())
    {
        $this->_view->render($view, $vars);
    }
    /**
     * This is the 404 Controller/Action
     * This action will called if no action found
     */
    public function notFoundAction()
    {
        $this->renderView('404.phtml');
    }
}