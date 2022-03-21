<?php

class WholesaleController extends Zend_Controller_Action
{
    public function indexAction(){
        $request = $this->getRequest();

        $registerForm = new Form_Wholesale();

        if($request->getPost('Registrieren')){
            if(!Zend_Auth::getInstance()->hasIdentity() || Zend_Auth::getInstance()->getIdentity()->is_wholesale){
                $this->_helper->redirector('index');
                return;
            }

            if($registerForm->isValid($request->getPost())){
                $user = Zend_Auth::getInstance()->getIdentity();
                $mail = new Zend_Mail('UTF-8');
                $mail->setFrom('mail@fairdirect.org', 'Sachspendenbörse');
                $mail->addTo('hoesel@derhoesel.de', 'Epelia');
                $mail->addTo('mail@epelia.com', 'Sachspendenbörse');
                $mail->setSubject('Spendenangebot-Bereich-Registrierung');
                $mail->setBodyText(
                    "Neue Spendeangebot-Bereich-Anmeldung:\n\n" . 
                    "Benutzer: " . $user->id . " (" . $user->email . ")\n" .
                    "Editieren: http://" . $request->getHttpHost() . "/admin/users/edit/id/" . $user->id . "/\n\n" .
                    "Angegebene Daten:\n" . 
                    "Firmenname: " . $request->getPost('company') . "\n" . 
                    "Ansprechpartner: " . $request->getPost('contact')
                );
                $registerForm->getValues(); // must call this the retrieve file
                $file = $registerForm->trade_certificate->getFilename();
                $certificate = new Zend_Mime_Part(file_get_contents($file));
                $certificate->filename = basename($file);
                $certificate->disposition = Zend_Mime::DISPOSITION_INLINE;
                $certificate->encoding = Zend_Mime::ENCODING_BASE64;

                $mail->addAttachment($certificate);
                $mail->send();

                $this->_helper->_redirector('success');
            }
        }

        $this->view->wholesaleForm = $registerForm;
    }

    public function showallAction(){
        // if user not enabled for wholesale, redirect to index
        if(!Zend_Auth::getInstance()->hasIdentity() || !Zend_Auth::getInstance()->getIdentity()->is_wholesale){
            $this->_helper->redirector('index');
            return;
        }
        $this->view->headTitle('Großhandels-Produkte | Epelia');
        $this->view->mainCategories = Model_MainCategory::getAll();
    }

    public function successAction(){
        $this->view->headTitle('Vielen Dank für Ihre Anmeldung für den Großhandelsbereich | Epelia');
    }
}
