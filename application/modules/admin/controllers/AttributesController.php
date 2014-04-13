<?php

class Admin_AttributesController extends Zend_Controller_Action
{

    public function init(){
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction(){
        $this->view->headTitle("Verwaltung");
           
        $this->view->attributes = Model_ProductAttribute::getAll();
    }

    public function editAction(){
        $form = new Admin_Form_Attributes();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $attribute = new Model_ProductAttribute($request->getPost());
                $attribute->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
               $attribute = Model_ProductAttribute::find($id);
                if($attribute){
                    $form->populate($attribute->toArray());
                }
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $attribute = Model_ProductAttribute::find($id);
        $attribut->delete();
        $this->_helper->redirector('index');
    }

}

