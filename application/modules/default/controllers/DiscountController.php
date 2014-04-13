<?php

class DiscountController extends Zend_Controller_Action
{
    public function indexAction(){

    }

    public function showallAction(){
        $this->view->headTitle('Discount-Produkte | Epelia');
        $this->view->groceryGroups = Model_ProductGroup::getByType('groceries', false, true, false);
        $this->view->drugstoreGroups = Model_ProductGroup::getByType('drugstore', false, true, false);
    }

}
