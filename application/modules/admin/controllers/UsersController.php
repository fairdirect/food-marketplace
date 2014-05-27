<?php

class Admin_UsersController extends Zend_Controller_Action
{

    public function init(){
         
    }

    public function indexAction()
    {
        $this->view->headTitle("User-Verwaltung");
        $this->view->users = Model_User::getAll();
    }

    public function ajaxgetusersAction(){
        $users = Model_User::getAll($this->getRequest()->getParam('iDisplayLength'), $this->getRequest()->getParam('iDisplayStart'), $this->getRequest()->getParam('sSearch'));
        $ret = array(
            'sEcho' => $this->getRequest()->getParam('sEcho') + 1,
            'iTotalRecords' => Model_User::getCount(),
            'iTotalDisplayRecords' => Model_User::getCount($this->getRequest()->getParam('sSearch')),
            'aaData' => array()
        );

        foreach($users as $user){
            switch($user->type){
                case 'customer':
                    $type = 'Kunde';
                    break;
                case 'shop':
                    $type = 'Shop';
                    break;
                case 'agent':
                    $type = 'Agent';
                    break;
                default:
                    $type = '';
                    break;
            }
            switch($user->status){
                case 'new':
                    $status = 'Neu';
                    break;
                case 'accepted':
                    $status = 'Bestätigt';
                    break;
                default:
                    $status = '';
                    break;
            }

            $ret['aaData'][] = array(
                htmlspecialchars($user->id),
                htmlspecialchars($user->email),
                htmlspecialchars($type),
                ($user->getMainBillingAddress()) ? $user->getMainBillingAddress()->toFormatedString() : '',
                ($user->is_wholesale) ? 'ja' : 'nein',
                htmlspecialchars($status),
                ($user->registered) ? date('d.m.Y', strtotime($user->registered)) : '-',
                ($user->last_login) ? date('d.m.Y', strtotime($user->last_login)) : '-',
                '<a href="/admin/users/edit/id/' . htmlspecialchars($user->id) . '/">Editieren</a><br /><a href="/admin/users/password/id/' . htmlspecialchars($user->id) . '">Passwort setzen</a><br /><a href="/admin/addresses/byuser/id/' . htmlspecialchars($user->id) . '/">Adressen</a><br /><a href="/admin/bankaccounts/byuser/id/' . htmlspecialchars($user->id) . '/">Konten</a><br /><a href="/admin/users/loginas/id/' . htmlspecialchars($user->id) . '/">Einloggen</a>'
            );
        }
        exit(json_encode($ret));
    }

    public function editAction(){
        $form = new Admin_Form_Users();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                if($id){
                    $user = Model_User::find($id);
                    $user->init($request->getPost());
                }
                else{
                    $user = new Model_User($request->getPost());
                }
                if($user->type != 'agent'){ // need to do this for woma_id constraint
                    $user->woma_id = null;
                }
                $user->save();
                $this->_redirect('/admin/users/');
            }
        }
        else{
            if($id){
               $user = Model_User::find($id);
                if($user){
                    $addressOptions = array();
                    foreach($user->getAddresses() as $address){
                        $addressOptions[$address->id] = $address->firstname . ' ' . $address->name . ', ' . $address->street . ' ' . $address->house . ' ' . $address->zip . ' ' . $address->city;
                    }
                    $form->getElement('main_delivery_address_id')->addMultiOptions($addressOptions);
                    $form->getElement('main_billing_address_id')->addMultiOptions($addressOptions);

                    $this->view->user = $user;
                    $form->populate($user->toFormArray());
                }
                else{
                    $this->view->user = null;
                }
            }
        }
        $this->view->form = $form;

        $addressForm = new Admin_Form_Addresses();
        $addressForm->populate(array('user_id' => $id));

        $bankAccountForm = new Admin_Form_BankAccounts();
        $bankAccountForm->populate(array('user_id' => $id));

        $this->view->addressForm = $addressForm;
        $this->view->bankAccountForm = $bankAccountForm;
    }

    public function passwordAction(){
        $form = new Admin_Form_Passwords();
        $request = $this->getRequest();
        $form->populate(array('user_id' => $request->getParam('id')));

        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                if($request->getParam('id')){
                    $user = Model_User::find($request->getParam('id'));
                    if($request->getPost('password1') != $request->getPost('password2')){
                        $this->view->error = 'Die angegebenen Passwörter stimmen nicht überein.';
                    }
                    else{
                        $user->password = hash('sha256', $request->getPost('password1') . '_epelia_' . $user->salt);
                        $user->save();
                        $this->_redirect('index');
                    }
                }
            }
        }
        $this->view->form = $form;
    }

    public function setdefaultdeliveryAction(){
        $request = $this->getRequest();
        $user = Model_User::find($request->getParam('id'));
        if($user){
            $address = Model_Address::find($request->getParam('addressid'));
            if($address){
                $user->main_delivery_address_id = $address->id;
                $user->save();
            }
        }
        $this->_redirect('/admin/addresses/byuser/id/' . $request->getParam('id'));
    }

    public function setdefaultbillingAction(){
        $request = $this->getRequest();
        $user = Model_User::find($request->getParam('id'));
        if($user){
            $address = Model_Address::find($request->getParam('addressid'));
            if($address){
                $user->main_billing_address_id = $address->id;
                $user->save();
            }
        }
        $this->_redirect('/admin/addresses/byuser/id/' . $request->getParam('id'));
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $user = Model_User::find($id);
        $user->delete();
        $this->_redirect('index');
    }

    public function loginasAction(){
        $id = $this->getRequest()->getParam('id');
        if(!$id){
            $this->_redirect('/admin/users/');
        }
        $user = Model_User::find($id);
        if($user){
            Zend_Session::namespaceUnset('Default');
            $auth = Zend_Auth::getInstance();
            $auth->getStorage()->write($user);
            $this->_redirect('/');
        }
        else{
            $this->_redirect('/admin/users/');
        }
    }

}
