<?php

class Admin_ContenttypesController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
           
        $this->view->types = Model_ContentType::getAll();
    }

    public function editAction(){
        $form = new Admin_Form_ContentTypes();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $type = new Model_ContentType($request->getPost());
                $type->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
               $type = Model_ContentType::find($id);
                if($type){
                    $form->populate($type->toArray());
                }
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $type = Model_ContentType::find($id);
        $type->delete();
        $this->_helper->redirector('index');
    }

}

