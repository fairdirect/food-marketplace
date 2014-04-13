<?php

class LoginController extends Zend_Controller_Action
{
    public function indexAction(){
        if(Zend_Auth::getInstance()->hasIdentity() && Zend_Auth::getInstance()->getIdentity()){
            $this->_redirect('/meinepelia/');
            return;
        }

        $form = new Form_Login();
        $this->view->assign('loginForm', $form);

        $request = $this->getRequest();
        if($request->getPost('Login')){
            if($form->isValid($request->getPost())){
                $session = new Zend_Session_Namespace('Default');
                if($this->_authenticate($request->getPost('email'), $request->getPost('password'))){
                    $session = new Zend_Session_Namespace('Default');
                    $session->shoppingCart->user_id = Zend_Auth::getInstance()->getIdentity()->id;
                    if($session->redirect){
                        $redirect = $session->redirect;
                        $session->redirect = false;
                        $this->_redirect($redirect);
                        exit();
                    }
                    else{
                        $this->_redirect('/meinepelia/');
                        exit();
                    }
                }
                else{
                    $this->view->error = 'Die angegebenen Daten sind inkorrekt.';
                }
            }
        }

        $registerForm = new Form_Register();

        if($request->getPost('Registrieren')){
            if($registerForm->isValid($request->getPost())){
                $exists = Model_User::findByEmail($request->getParam('email', true));
                if($exists){
                    $this->view->registerError = 'Die angegebenen E-Mail-Adresse ist bereits vorhanden.';
                }
                else{
                    if($request->getPost('password1') != $request->getPost('password2')){
                        $this->view->registerError = 'Die angegebenen Passwörter stimmen nicht überein.';
                    }
                    else{
                        if(!$request->getPost('agb')){
                            $this->view->registerError = 'AGB und Datenschutzrichtlinien müssen bestätigt werden';
                        }
                        else{
                            $user = new Model_User($request->getPost());
                            $user->salt = md5(uniqid('', true));
                            $user->password = md5($request->getPost('password1') . '_epelia_' . $user->salt);
                            $user->registered = date('Y-m-d', time());
                            try{
                                $user->save();
                                $address = new Model_Address(array(
                                    'gender' => $request->getPost('gender'),
                                    'firstname' => $request->getPost('firstname'),
                                    'name' => $request->getPost('name'),
                                    'company' => $request->getPost('company'),
                                    'street' => $request->getPost('street'),
                                    'house' => $request->getPost('housenumber'),
                                    'zip' => $request->getPost('zip_code'),
                                    'city' => $request->getPost('city'),
                                    'country' => $request->getPost('country'),
                                    'user_id' => $user->id
                                ));
                                $address->save();
                                $user->main_delivery_address_id = $address->id;
                                $user->main_billing_address_id = $address->id;
                                $user->save();

                            } catch(Exception $e){
                                $this->_helper->_redirector('failure');
                            }
                            try{
                                $registerMail = Model_Email::find('register');
                                $mail = new Zend_Mail('UTF-8');
                                $content = str_replace('#registerLink#', 'http://' . $_SERVER['HTTP_HOST'] . '/login/confirm/id/' . $user->id . '/code/' . md5($user->email . '_epelia_' . $user->salt) . '/', $registerMail->content);
                                $mail->setBodyText(strip_tags($content));
                                $mail->setFrom('mail@epelia.com', 'Epelia');
                                $mail->addTo($user->email);
                                $mail->setSubject($registerMail->subject);
                                $mail->send();
                            } catch(Exception $e){
                                $this->_helper->_redirector('failure');
                            }
                            $this->_helper->_redirector('success');
                        }
                    }
                }
            }
        }

        $this->view->assign('registerForm', $registerForm);
    }

    public function logoutAction(){
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::namespaceUnset('Default');
        $this->_redirect('/');
    }


    public function confirmAction(){
        if(!$this->getRequest()->getParam('id') || !$this->getRequest()->getParam('id')){
            $this->_helper->_redirector('confirmfailure');
        }
        $user = Model_User::find($this->getRequest()->getParam('id'));
        if(!$user){
            $this->_helper->_redirector('confirmfailure');
        }
        else{
            if(md5($user->email . '_epelia_' . $user->salt) != $this->getRequest()->getParam('code') || $user->status != 'new'){
                $this->_helper->_redirector('confirmfailure');
            }
            else{
                $user->status = 'accepted';
                $user->save();
                $this->_helper->_redirector('confirmsuccess');
            }
        }
    }

    private function _authenticate($username, $password){
        $dbAdapter = Model_User::getDbTable()->getAdapter();
        $adapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $adapter->setTableName(Model_User::getDbTable()->getTableName());
        $adapter->setIdentityColumn('email');
        $adapter->setCredentialColumn('password');
        $adapter->setCredentialTreatment("MD5(? || '_epelia_' || salt)");
        
        $adapter->setIdentity($username);
        $adapter->setCredential($password);
        $adapter->getDbSelect()->where("status = 'accepted'");

        $auth = Zend_Auth::getInstance();
        $res = $auth->authenticate($adapter);

        if($res->isValid()){
            $user = new Model_User((array) $adapter->getResultRowObject());
            $user->last_login = date('Y-m-d', time());
            $user->save();
            $auth->getStorage()->write($user);
            return true;
        }
        else{
            return $this->_oldAuth($username, $password); // Login failed, trying old password
        }
    }

    private function _oldAuth($username, $password){
        $dbAdapter = Model_User::getDbTable()->getAdapter();
        $adapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $adapter->setTableName(Model_User::getDbTable()->getTableName());
        $adapter->setIdentityColumn('email');
        $adapter->setCredentialColumn('old_password_hash');
        $adapter->setCredentialTreatment("MD5(?)");
        
        $adapter->setIdentity($username);
        $adapter->setCredential($password);
        $adapter->getDbSelect()->where("status = 'accepted'");

        $auth = Zend_Auth::getInstance();
        $res = $auth->authenticate($adapter);

        if($res->isValid()){
            $user = new Model_User((array) $adapter->getResultRowObject());
            $user->last_login = date('Y-m-d', time());

            // updateing to new auth
            $user->salt = md5(uniqid('', true));
            $user->password = md5($password . '_epelia_' . $user->salt);
            $user->old_password_hash = '';

            $user->save();
            $auth->getStorage()->write($user);
            return true;
        }
        return false;

    }

    public function successAction(){
        $this->view->headTitle("Registrierung erfolgreich | Epelia");
    }

    public function failureAction(){
        $this->view->headTitle("Registrierung fehlgeschlagen | Epelia");
    }

    public function confirmsuccessAction(){
        $this->view->headTitle("Registrierungs-Bestätigung erfolgreich | Epelia");
    }

    public function confirmfailureAction(){
        $this->view->headTitle("Registrierungs-Bestätigung fehlgeschlagen | Epelia");
    }

    public function resetpasswordAction(){
        $this->view->headTitle("Password zurücksetzen");
    }

    public function sendresetpasswordmailAction(){
        $this->view->headTitle("Passwort Mail versendet");
        $user = Model_User::findByEmail($this->getRequest()->getParam('email'));
        if(!$user){
            return;
        }
        $user->password_reset_token = md5($user->email . '_epelia_' . time());
        $user->save();
        try{
            $resetMail = Model_Email::find('resetPassword');
            $mail = new Zend_Mail('UTF-8');
            $content = str_replace('#resetPasswordLink#', 'http://' . $_SERVER['HTTP_HOST'] . '/login/resetpasswordconfirm/id/' . $user->id . '/code/' . $user->password_reset_token . '/', $resetMail->content);
            $mail->setBodyText(strip_tags($content));
            $mail->setFrom('mail@epelia.com', 'Epelia');
            $mail->addTo($user->email);
            $mail->setSubject($resetMail->subject);
            $mail->send();
        } catch(Exception $e){
            // dont inform the user if something went wrong
        }
    }

    public function resetpasswordconfirmAction(){
        $this->view->headTitle("Passwort zurücksetzen");

        $changePasswordForm = new Form_Changepassword();
        $changePasswordForm->populate(array('id' => $this->getRequest()->getParam('id'), 'password_reset_token' => $this->getRequest()->getParam('code')));
        if($this->getRequest()->getPost('changepassword')){
            if($changePasswordForm->isValid($this->getRequest()->getPost())){
                $user = Model_User::find($this->getRequest()->getPost('id'));
                if(!$user || $user->password_reset_token != $this->getRequest()->getPost('password_reset_token')){
                    $this->view->changePasswordError = 'Es ist ein Fehler aufgetreten.';
                }
                else{
                    if($this->getRequest()->getPost('password1') != $this->getRequest()->getPost('password2')){
                        $this->view->registerError = 'Die angegebenen Passwörter stimmen nicht überein.';
                    }
                    else{
                        $user->password = md5($this->getRequest()->getPost('password1') . '_epelia_' . $user->salt);
                        $user->password_reset_token = '';
                        $user->save();
                        $this->_helper->_redirector('resetsuccess');
                        return;
                    }
                }
            }
        }
        else{
            $user = Model_User::find($this->getRequest()->getParam('id'));
            if(!$user || !$this->getRequest()->getParam('code') || !$user->password_reset_token || $user->password_reset_token != $this->getRequest()->getParam('code')){
                $this->_helper->_redirector('resetfailure');
                return;
            }
        }

        $this->view->assign('changePasswordForm', $changePasswordForm);
    }

    public function resetsuccessAction(){
        $this->view->headTitle("Passwort erfolgreich geändert");
    }

    public function resetfailureAction(){
        $this->view->headTitle("Passwort-Reset fehlgeschlagen");
    }


}
