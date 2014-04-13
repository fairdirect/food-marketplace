<?php

class Admin_UnitsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
           
        $this->view->units = Model_Unit::getAll();
    }

    public function editAction(){
        $form = new Admin_Form_Units();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $unit = new Model_Unit($request->getPost());
                $unit->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
               $unit = Model_Unit::find($id);
                if($unit){
                    $form->populate($unit->toArray());
                }
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $unit = Model_Unit::find($id);
        $unit->delete();
        $this->_helper->redirector('index');
    }

}

