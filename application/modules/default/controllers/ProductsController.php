<?php

class ProductsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        throw new Zend_Controller_Action_Exception('This page does not exist', 404);
    }


    public function showAction(){
        if(!$this->getRequest()->getParam('id')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $product = Model_Product::find($this->getRequest()->getParam('id'));
        if(!$product){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($product->name . ' | Epelia');
        $this->view->product = $product;
    }
}

