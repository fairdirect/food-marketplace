<?php

class RegionsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->availableRegions = Model_Region::getCountriesWithRegions();
      }

    public function changeregionAction() {
        if(!$this->getRequest()->getParam('id')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $region = Model_Region::find($this->getRequest()->getParam('id'));
        if(!region){
            throw new Zend_Controller_Action_Exception('No region with this ID found', 404);
        }
        $session = new Zend_Session_Namespace('Default');
        $session->region = $region;
        $this->_redirect('/');
    }

}