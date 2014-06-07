<?php

class SearchController extends Zend_Controller_Action
{
    public function init(){
        $this->limit = null;
        $this->offset = ($this->getRequest()->getParam('page')) ? $this->limit * $this->getRequest()->getParam('page') : null;
    }

    public function indexAction(){
        if($this->getRequest()->getParam('query')){
            $this->_redirect('/search/' . $this->getRequest()->getParam('query') . '/');
        }
        else{
            $this->_helper->redirector('noresult');
        }
    }

    public function searchAction(){
        $request = $this->getRequest();
        if(!$request->getParam('query')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }

        $onlyBio = $onlyDiscount = $onlyWholesale = false;
        if($request->getParam('bio') || $request->getParam('discount') || $request->getParam('wholesale')){
            $onlyBio = ($request->getParam('bio') == 'bio');
            $onlyDiscount = ($request->getParam('discount') == 'discount');
            $onlyWholesale = ($request->getParam('wholesale') == 'wholesale');
        }
        $page = ($request->getParam('page')) ? $request->getParam('page') : '1';

        $productCount = count(Model_Product::findBySearchString($this->getRequest()->getParam('query'), null, null, $onlyBio, $onlyDiscount, $onlyWholesale));
        if(!$productCount){
            $this->_helper->redirector('noresult');
        }

        $this->view->headTitle('Suchergebnisse für ' . $this->getRequest()->getParam('query') . ' | Epelia');
        $this->view->searchQuery = $this->getRequest()->getParam('query');
        $this->view->productCount = $productCount;
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->page = $page;
    }

    public function noresultAction(){
        $this->getResponse()->setHttpResponseCode(404);
    }

}
