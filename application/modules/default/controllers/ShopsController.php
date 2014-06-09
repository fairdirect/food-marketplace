<?php

class ShopsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        throw new Zend_Controller_Action_Exception('This page does not exist', 404);
    }

    public function showAction(){
        if(!$this->getRequest()->getParam('url')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $shop = Model_Shop::findByUrl($this->getRequest()->getParam('url'));
        if(!$shop){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($shop->name . ' | Epelia');
        $this->view->shop = $shop;
    }

    public function areasearchAction(){
        $request = $this->getRequest();
        $zip = $request->getPost('zip');
        $country = $request->getPost('country');
        $distance = $request->getPost('distance');
        if(!$zip || !$country || !$distance || !in_array($country, array('DE', 'AT', 'CH'))){
            $this->_redirect('/');
        }
        $shops = Model_Shop::findByPlzAndDistance($zip, $country, $distance);
        $this->view->shops = $shops;
        $this->view->zip = $zip;
        $this->view->country = $country;
        $this->view->distance = $distance;
    }
}

