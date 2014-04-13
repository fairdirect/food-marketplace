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

    public function searchAction()
    {
        if(!$this->getRequest()->getParam('query')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $products = Model_Product::findBySearchString($this->getRequest()->getParam('query', $this->limit, $this->offset));
        if(!$products){
            $this->_helper->redirector('noresult');
        }

        $this->view->headTitle('Suchergebnisse für ' . $this->getRequest()->getParam('query') . ' | Epelia');
        $this->view->searchQuery = $this->getRequest()->getParam('query');
        $this->view->products = $products;
    }

    public function noresultAction(){
        $this->getResponse()->setHttpResponseCode(404);
    }

}
