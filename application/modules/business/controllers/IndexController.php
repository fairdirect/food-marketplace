<?php

class Business_IndexController extends Zend_Controller_Action{

    public function init(){
        $this->user = Model_User::find(Zend_Auth::getInstance()->getIdentity()->id);
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
        $this->view->shop = $this->user->getShop();        
    }

}
