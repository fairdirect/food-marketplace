<?php

class Admin_EmailsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
           
        $this->view->emails = Model_Email::getAll();
    }

    public function editAction(){
        $form = new Admin_Form_Emails();

        $request = $this->getRequest();
        $name = $request->getParam('name');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $email = new Model_Email($request->getPost());
                $email->update();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($name){
                $email = Model_Email::find($name);
                if($email){
                    $form->populate($email->toArray());
                }
                else{
                    $this->_helper->redirector('index');
                }
            }
        }
        $this->view->form = $form;
    }

}
