<?php
/**
 * @author    Wayne Le
 */
class IndexController extends ActionController
{
    public function indexAction()
    {
        // Send variables to view
        $this->getView()->render();
    }
}