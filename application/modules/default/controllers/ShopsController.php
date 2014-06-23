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

    public function showallAction(){
        $request = $this->getRequest();
        if(!$request->getParam('url')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $shop = Model_Shop::findByUrl($request->getParam('url'));

        $onlyBio = $onlyDiscount = $onlyWholesale = false;
        if($request->getParam('bio') || $request->getParam('discount') || $request->getParam('wholesale')){
            $onlyBio = ($request->getParam('bio') == 'bio');
            $onlyDiscount = ($request->getParam('discount') == 'discount');
            $onlyWholesale = ($request->getParam('wholesale') == 'wholesale');
        }
        $page = ($request->getParam('page')) ? $request->getParam('page') : '1';

        if(!$shop){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($shop->name . ' | Epelia');
        $this->view->shop = $shop;
        $this->view->productCount = count($shop->getProducts(null, null, $onlyBio, $onlyDiscount, $onlyWholesale));
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->page = $page;
    }


    public function showshopcatAction(){
        $request = $this->getRequest();
        if(!$request->getParam('url') || !$request->getParam('catid')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $shop = Model_Shop::findByUrl($request->getParam('url'));
        $cat = Model_ProductCategory::find($request->getParam('catid'));

        $onlyBio = $onlyDiscount = $onlyWholesale = false;
        if($request->getParam('bio') || $request->getParam('discount') || $request->getParam('wholesale')){
            $onlyBio = ($request->getParam('bio') == 'bio');
            $onlyDiscount = ($request->getParam('discount') == 'discount');
            $onlyWholesale = ($request->getParam('wholesale') == 'wholesale');
        }
        $page = ($request->getParam('page')) ? $request->getParam('page') : '1';

        if(!$shop){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($shop->name . ' ' . $cat->name . ' | Epelia');
        $this->view->shop = $shop;
        $this->view->category = $cat;
        $this->view->productCount = count($shop->getProductsByCategory($cat->id, null, null, $onlyBio, $onlyDiscount, $onlyWholesale));
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->page = $page;
    }

    public function showshopattrAction(){
        $request = $this->getRequest();
        if(!$request->getParam('url') || !$request->getParam('attrid')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $shop = Model_Shop::findByUrl($request->getParam('url'));
        $attr = Model_ProductAttribute::find($request->getParam('attrid'));

        $onlyBio = $onlyDiscount = $onlyWholesale = false;
        if($request->getParam('bio') || $request->getParam('discount') || $request->getParam('wholesale')){
            $onlyBio = ($request->getParam('bio') == 'bio');
            $onlyDiscount = ($request->getParam('discount') == 'discount');
            $onlyWholesale = ($request->getParam('wholesale') == 'wholesale');
        }
        $page = ($request->getParam('page')) ? $request->getParam('page') : '1';

        if(!$shop){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $this->view->headTitle($shop->name . ' ' . $attr->name . ' | Epelia');
        $this->view->shop = $shop;
        $this->view->attribute = $attr;
        $this->view->productCount = count($shop->getProductsByAttribute($attr->id, null, null, $onlyBio, $onlyDiscount, $onlyWholesale));
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->page = $page;
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

