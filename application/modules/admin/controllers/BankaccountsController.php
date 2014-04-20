<?php

class Admin_BankaccountsController extends Zend_Controller_Action{

    public function init(){
    }

    public function indexAction()
    {
        $this->_helper->redirector('/admin/');
    }

    public function byuserAction(){
        $user = Model_User::find($this->getRequest()->getParam('id'));
        $this->view->headTitle("Bankverbindungen für Benutzer " . $user->email);
        $this->view->user = $user;
        $this->view->bankaccounts = $user->getBankAccounts();
    }

    public function editAction(){
        $form = new Admin_Form_BankAccounts();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $bankAccount = new Model_BankAccount($request->getPost());
                $bankAccount->save();
                $this->_redirect('/admin/bankaccounts/byuser/id/' . $request->getParam('user_id') . '/');
            }
        }
        else{
            if($id){
               $bankAccount = Model_BankAccount::find($id);
                if($bankAccount){
                    $form->populate($bankAccount->toArray());
                }
            }
            else{
                $form->populate(array('user_id' => $request->getParam('user_id')));
            }
        }
        $this->view->form = $form;
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $bankAccount = Model_BankAccount::find($id);
        $bankAccount->delete();
        $this->_helper->redirector('index');
    }
}
