<?php

class BioController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->headTitle('Bio Lebensmittel | Epelia');
	}

    public function showallAction(){
        $this->view->headTitle('Alle Bio-Produkte | Epelia');
        $this->view->groceryGroups = Model_ProductGroup::getByType('groceries', true, false, false);
        $this->view->drugstoreGroups = Model_ProductGroup::getByType('drugstore', true, false, false);
    }


}
