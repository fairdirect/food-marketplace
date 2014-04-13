<?php

class Admin_CountriesController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
           
        $this->view->countries = Model_Country::getAll();
    }

    public function editAction(){
        $form = new Admin_Form_Countries();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $country = new Model_Country($request->getPost());
                $country->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
               $country = Model_Country::find($id);
                if($country){
                    $form->populate($country->toArray());
                }
            }
        }

        $this->view->form = $form;
    }

}

