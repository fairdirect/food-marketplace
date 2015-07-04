<?php

class BioController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->view->headTitle('Bio Lebensmittel | Epelia');
	}

    public function showallAction(){
        $this->view->headTitle('Alle Bio-Produkte | Epelia');
        $this->view->mainCategories = Model_MainCategory::getAll();
    }


}
