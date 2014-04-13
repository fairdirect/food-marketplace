<?php

class Admin_AddressesController extends Zend_Controller_Action{

    public function init(){
    }

    public function indexAction()
    {
        $this->_redirect('/admin/');
    }

    public function byuserAction(){
        $user = Model_User::find($this->getRequest()->getParam('id'));
        $this->view->headTitle("Addressen für Benutzer " . ($user->username) ? $user->username : $user->email);
        $this->view->user = $user;
        $this->view->addresses = $user->getAddresses();
    }

    public function editAction(){
        $form = new Admin_Form_Addresses();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $address = new Model_Address($request->getPost());
                $address->save();
                $this->_redirect('/admin/addresses/byuser/id/' . $request->getParam('user_id') . '/');
            }
        }
        else{
            if($id){
               $address = Model_Address::find($id);
                if($address){
                    $form->populate($address->toArray());
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
        $address = Model_Address::find($id);
        $address->delete();
        $this->_helper->redirector('index');
    }
}
