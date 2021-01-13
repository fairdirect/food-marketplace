<?php

class CatsController extends Zend_Controller_Action
{
    public function init() {
        if(!Model_Region::getCurrentRegion()) { // region selected
            $this->_redirect('/regions/');
        }
    }

    public function indexAction()
    {
      throw new Zend_Controller_Action_Exception('This page does not exist', 404);
    }

    public function showcatAction(){
        $request = $this->getRequest();
        if(!$request->getParam('id')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $onlyBio = $onlyDiscount = $onlyWholesale = false;
        $productType = null;
        if($request->getParam('bio') || $request->getParam('discount') || $request->getParam('wholesale') || $request->getParam('producttype')){
            $onlyBio = ($request->getParam('bio') == 'bio');
            $onlyDiscount = ($request->getParam('discount') == 'discount');
            $onlyWholesale = ($request->getParam('wholesale') == 'wholesale');
            $productType = $request->getParam('producttype');
        }
        $page = ($request->getParam('page')) ? $request->getParam('page') : '1';

        $cat = Model_ProductCategory::find($request->getParam('id'));
        if(!$cat){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($cat->name . ' | Epelia');
        $this->view->productCount = $cat->getProductCount($onlyBio, $onlyDiscount, $onlyWholesale, true, false, $productType);
        $this->view->cat = $cat;
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->productType = $productType;
        $this->view->page = $page;

    }

    public function showallAction(){
        $this->view->mainCategories = Model_MainCategory::getAll();
    }
}

