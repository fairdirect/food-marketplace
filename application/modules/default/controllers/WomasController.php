<?php

class WomasController extends Zend_Controller_Action
{
    public function indexAction()
    {
        throw new Zend_Controller_Action_Exception('This page does not exist', 404);
    }

    public function showAction(){
        if(!$this->getRequest()->getParam('id')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $woma = Model_Woma::find($this->getRequest()->getParam('id'));
        if(!$woma){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($woma->name . ' | Epelia');
        $this->view->woma = $woma;
    }

    public function showallAction(){
        $request = $this->getRequest();
        if(!$request->getParam('id')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $woma = Model_Woma::find($request->getParam('id'));

        $onlyBio = $onlyDiscount = $onlyWholesale = false;
        if($request->getParam('bio') || $request->getParam('discount') || $request->getParam('wholesale')){
            $onlyBio = ($request->getParam('bio') == 'bio');
            $onlyDiscount = ($request->getParam('discount') == 'discount');
            $onlyWholesale = ($request->getParam('wholesale') == 'wholesale');
        }
        $page = ($request->getParam('page')) ? $request->getParam('page') : '1';

        if(!$woma){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($woma->name . ' | Epelia');
        $this->view->woma = $woma;
        $this->view->productCount = count($woma->getProducts(null, null, $onlyBio, $onlyDiscount, $onlyWholesale));
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->page = $page;
    }


    public function showwomacatAction(){
        $request = $this->getRequest();
        if(!$request->getParam('id') || !$request->getParam('catid')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $woma = Model_Woma::find($request->getParam('id'));
        $cat = Model_ProductCategory::find($request->getParam('catid'));

        $onlyBio = $onlyDiscount = $onlyWholesale = false;
        if($request->getParam('bio') || $request->getParam('discount') || $request->getParam('wholesale')){
            $onlyBio = ($request->getParam('bio') == 'bio');
            $onlyDiscount = ($request->getParam('discount') == 'discount');
            $onlyWholesale = ($request->getParam('wholesale') == 'wholesale');
        }
        $page = ($request->getParam('page')) ? $request->getParam('page') : '1';

        if(!$woma){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($woma->name . ' ' . $cat->name . ' | Epelia');
        $this->view->woma = $woma;
        $this->view->category = $cat;
        $this->view->productCount = count($woma->getProductsByCategory($cat->id, null, null, $onlyBio, $onlyDiscount, $onlyWholesale));
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->page = $page;
    }

    public function showwomaattrAction(){
        $request = $this->getRequest();
        if(!$request->getParam('id') || !$request->getParam('attrid')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $woma = Model_Woma::find($request->getParam('id'));
        $attr = Model_ProductAttribute::find($request->getParam('attrid'));

        $onlyBio = $onlyDiscount = $onlyWholesale = false;
        if($request->getParam('bio') || $request->getParam('discount') || $request->getParam('wholesale')){
            $onlyBio = ($request->getParam('bio') == 'bio');
            $onlyDiscount = ($request->getParam('discount') == 'discount');
            $onlyWholesale = ($request->getParam('wholesale') == 'wholesale');
        }
        $page = ($request->getParam('page')) ? $request->getParam('page') : '1';

        if(!$woma){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($woma->name . ' ' . $attr->name . ' | Epelia');
        $this->view->woma = $woma;
        $this->view->attribute = $attr;
        $this->view->productCount = count($woma->getProductsByAttribute($attr->id, null, null, $onlyBio, $onlyDiscount, $onlyWholesale));
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->page = $page;
    }

}

