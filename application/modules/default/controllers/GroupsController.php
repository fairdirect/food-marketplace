<?php

class GroupsController extends Zend_Controller_Action
{
    public function indexAction()
    {
        throw new Zend_Controller_Action_Exception('This page does not exist', 404);
    }

    public function showgroupAction(){
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

        $group = Model_ProductGroup::find($this->getRequest()->getParam('id'));
        if(!$group){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }

        $this->view->productCount = $group->getProductCount($onlyBio, $onlyDiscount, $onlyWholesale);
        $this->view->headTitle($group->name . ' | Epelia');
        $this->view->group = $group;
        $this->view->onlyBio = $onlyBio;
        $this->view->onlyDiscount = $onlyDiscount;
        $this->view->onlyWholesale = $onlyWholesale;
        $this->view->page = $page;
    }
}

