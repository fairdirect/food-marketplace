<?php

class Business_IndexController extends Zend_Controller_Action{

    public function init(){
        $this->user = Model_User::find(Zend_Auth::getInstance()->getIdentity()->id);
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
        $this->view->shop = $this->user->getShop();        
        $this->view->inProcessOrders = Model_Order::findByShop($this->user->getShop()->id, 'in_process');
        $this->view->completeOrders = Model_Order::findByShop($this->user->getShop()->id, 'complete');
    }

}
