<?php

class AttributesController extends Zend_Controller_Action
{
    public function indexAction(){
        $this->view->allergenes = Model_ProductAttribute::getByType('allergen');
        $this->view->additives = Model_ProductAttribute::getByType('additive');
    }

    public function showattributeAction(){
        $request = $this->getRequest();
        if(!$request->getParam('id')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $onlyBio = $onlyDiscount = $onlyWholesale = false;
        if($request->getParam('bio') || $request->getParam('discount') || $request->getParam('wholesale')){
            $onlyBio = ($request->getParam('bio') == 'bio');
            $onlyDiscount = ($request->getParam('discount') == 'discount');
            $onlyWholesale = ($request->getParam('wholesale') == 'wholesale');
        }
        $page = ($request->getParam('page')) ? $request->getParam('page') : '1';

        $attribute = Model_ProductAttribute::find($this->getRequest()->getParam('id'));
        if(!$attribute){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($attribute->name . ' | Epelia');
        $this->view->productCount = $attribute->getProductCount($onlyBio, $onlyDiscount, $onlyWholesale);
        $this->view->attribute = $attribute;
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->page = $page;

    }
}

