<?php

class ErrorController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

	public function errorAction() 
    { 
        // Ensure the default view suffix is used so we always return good 
        // content
        $this->_helper->viewRenderer->setViewSuffix('phtml');
 
        // use shiny exception handler view, if configured as:
        // resources.frontController.errorview = shiny
        if ($this->getInvokeArg('errorview') && $this->getInvokeArg('errorview') != 'error') {
            $this->_helper->layout->disableLayout();
            $this->_helper->viewRenderer($this->getInvokeArg('errorview'));
        }
 
        // Grab the error object from the request
        $errors = $this->_getParam('error_handler'); 
 
        // $errors will be an object set as a parameter of the request object, 
        // type is a property
        switch ($errors->type) { 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER: 
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION: 
 
                // 404 error -- controller or action not found 
                $this->getResponse()->setHttpResponseCode(404); 
                $this->view->message = '404 Page not found'; 
                break; 
            default: 
                // application error 
                $this->getResponse()->setHttpResponseCode(500); 
                $this->view->message = '500 Application error'; 
                break; 
        } 
 
        // pass the environment to the view script so we can conditionally 
        // display more/less information
        $this->view->env       = $this->getInvokeArg('env'); 
 
        // pass the actual exception object to the view
//        if($this->getResponse()->getHttpResponseCode() != 404){
            $this->view->exception = $errors->exception; 
  //      }
 
        // pass the request to the view
        $this->view->request   = $errors->request; 
    }


}
