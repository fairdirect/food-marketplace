<?php

class Admin_GroupsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
           
        $this->view->groceryGroups = Model_ProductGroup::getByType('groceries', false, false, false, false);
        $this->view->drugstoreGroups = Model_ProductGroup::getByType('drugstore', false, false, false, false);
    }

    public function editAction(){
        $form = new Admin_Form_Groups();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $group = new Model_ProductGroup($request->getPost());
                $group->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
               $group = Model_ProductGroup::find($id);
                if($group){
                    $form->populate($group->toArray());
                }
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $gr = Model_ProductGroup::find($id);
        $gr->delete();
        $this->_helper->redirector('index');
    }


}

