<?php

class Business_IndexController extends Zend_Controller_Action{

    public function init(){
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
        
    }

}
