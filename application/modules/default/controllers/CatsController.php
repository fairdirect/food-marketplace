<?php

class CatsController extends Zend_Controller_Action
{
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
        if($request->getParam('bio') || $request->getParam('discount') || $request->getParam('wholesale')){
            $onlyBio = ($request->getParam('bio') == 'bio');
            $onlyDiscount = ($request->getParam('discount') == 'discount');
            $onlyWholesale = ($request->getParam('wholesale') == 'wholesale');
        }
        $page = ($request->getParam('page')) ? $request->getParam('page') : '1';

        $cat = Model_ProductCategory::find($request->getParam('id'));
        if(!$cat){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($cat->name . ' | Epelia');
        $this->view->productCount = $cat->getProductCount($onlyBio, $onlyDiscount, $onlyWholesale);
        $this->view->cat = $cat;
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->page = $page;

    }

    public function showallAction(){
        $this->view->mainCategories = Model_MainCategory::getAll();
    }
}

