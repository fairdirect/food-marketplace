<?php

class VerkaufenController extends Zend_Controller_Action
{
    public function indexAction(){
    }

    public function registerAction(){
        $request = $this->getRequest();
        $form = new Form_RegisterSeller();

        if($request->getPost('Registrieren')){
            if($form->isValid($request->getPost())){
                $exists = Model_User::findByEmail($request->getParam('email', true));
                if($exists){
                    $this->view->registerError = 'Die angegebenen E-Mail-Adresse ist bereits vorhanden.';
                }
                else{
                    if($request->getPost('password1') != $request->getPost('password2')){
                        $this->view->registerError = 'Die angegebenen Passwörter stimmen nicht überein.';
                    }
                    else{
                        $user = new Model_User(array(
                            'email' => $request->getPost('email'),
                            'type' => 'shop'
                        ));
                        $user->salt = hash('sha256', uniqid('', true));
                        $user->password = hash('sha256', $request->getPost('password1') . '_epelia_' . $user->salt);
                        try{
                            $user->save();
                        } catch(Exception $e){
                            $this->_helper->_redirector('failure');
                        }
                        $shop = new Model_Shop(array(
                            'user_id' => $user->id,
                            'name' => $request->getPost('shopname'),
                            'company' => $request->getPost('company'),
                            'representative' => $request->getPost('gender') . ' ' . $request->getPost('firstname') . ' ' . $request->getPost('name'),
                            'phone' => $request->getPost('phone'),
                            'country' => $request->getPost('country')
                        ));
                        $shop->url = Epelia_Helper::make_url($shop->name);
                        $urlExitst = Model_Shop::findByUrl($shop->url);
                        if($urlExitst){
                            $counter = 1;
                            $newUrl = $shop->url . '-' . $counter;
                            while(Model_Shop::findByUrl($newUrl)){
                                $counter++;
                                $newUrl = $shop->url . '-' . $counter;
                            }
                            $shop->url = $newUrl;
                        }
                        try{
                            $shop->save();
                        } catch(Exception $e){
                            $this->_helper->_redirector('failure');
                        }
                        try{
                            $registerMail = Model_Email::find('registerShop');
                            $mail = new Zend_Mail('UTF-8');
                            $content = str_replace(array('#salutation#', '#lastname#', '#registerLink#'), array($request->getPost('gender'), $request->getPost('name'), 'http://' . $request->getHttpHost() . '/login/confirm/id/' . $user->id . '/code/' . hash('sha256', $user->email . '_epelia_' . $user->salt) . '/'), $registerMail->content);
                            $mail->setBodyText(strip_tags($content));
                            $mail->setFrom('mail@epelia.com', 'Epelia');
                            $mail->addTo($user->email);
                            $mail->setSubject($registerMail->subject);
                            $at = $mail->createAttachment(file_get_contents(APPLICATION_PATH . '/files/agb.pdf'));
                            $at->filename = 'agb.pdf';
                            $mail->send();
                        } catch(Exception $e){
                            $this->_helper->_redirector('failure');
                        }

                        $mail = new Zend_Mail('UTF-8');
                        $mail->setFrom('mail@epelia.com', 'Epelia');
                        $mail->addTo('hoesel@derhoesel.de', 'Epelia');
                        $mail->addTo('mail@epelia.com', 'Epelia');
                        $mail->setSubject('Verkäufer Registrierung');
                        $mail->setBodyText(
                            "Neue Verkäufer-Anmeldung:\n\n" . 
                            "Firma: " . $request->getPost('company') . "\n" .
                            "Vorname: " . $request->getPost('firstname') . "\n" .
                            "Nachname: " . $request->getPost('name') . "\n" . 
                            "Telefon: " . $request->getPost('phone') . "\n" . 
                            "E-Mail: " . $request->getPost('email') . "\n" . 
                            "Land: " . $request->getPost('country') . "\n" . 
                            "Webseite: " . $request->getPost('website') . "\n\n" . 
                            "User editieren: http://" . $request->getHttpHost() . "/admin/users/edit/id/" . $user->id . "/\n". 
                            "Shop editieren: http://" . $request->getHttpHost() . "/admin/shops/edit/id/" . $shop->id . "/"
                        );
                        $mail->send();
                        $this->_helper->_redirector('success');
                    }
                }
            }
        }
        $this->view->form = $form;
    }

    public function successAction(){
        $this->view->headTitle('Vielen Dank für Ihre Registrierung | Epelia');
    }

    public function failureAction(){
        $this->view->headTitle('Es ist ein Fehler aufgetreten | Epelia');
    }

}
