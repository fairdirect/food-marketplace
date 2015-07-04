<?php

class DiscountController extends Zend_Controller_Action
{
    public function indexAction(){

    }

    public function showallAction(){
        $this->view->headTitle('Discount-Produkte | Epelia');
        $this->view->mainCategories = Model_MainCategory::getAll();
    }

}
