<?php

class Admin_MaincategoriesController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
        $this->view->mainCategories = Model_MainCategory::getAll();
    }

    public function editAction(){
        $form = new Admin_Form_Maincategories();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $group = new Model_MainCategory($request->getPost());
                $group->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
               $mainCategory = Model_MainCategory::find($id);
                if($mainCategory){
                    $form->populate($mainCategory->toArray());
                }
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $gr = Model_MainCategory::find($id);
        $gr->delete();
        $this->_helper->redirector('index');
    }


}

