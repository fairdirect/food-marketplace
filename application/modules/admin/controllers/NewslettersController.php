<?php

class Admin_NewslettersController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
           
        $this->view->newsletters = Model_Newsletter::getAll();
    }

    public function editAction(){
        $form = new Admin_Form_Newsletters();
        $sendForm = new Admin_Form_NewslettersSend();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $newsletter = new Model_Newsletter($request->getPost());
                $newsletter->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
                $newsletter = Model_Newsletter::find($id);
                if($newsletter){
                    $form->populate($newsletter->toArray());
                }
                $sendForm->populate(array('id' => $id));
            }
        }
        $this->view->form = $form;
        $this->view->sendForm = $sendForm;
    }

    public function sendAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        if(!$id){
            exit(json_encode(array('suc' => false, 'message' => 'ID is missing')));
        }
        $recipients = $request->getParam('recipients');
        if(!$recipients){
            exit(json_encode(array('suc' => false, 'message' => 'Recipients are missing')));
        }
        if(!$recipients == 'SINGLE_ADDRESS' && !$request->getParam('testmail')){
            exit(json_encode(array('suc' => false, 'message' => 'Testmail is missing')));
        }
        $newsletter = Model_Newsletter::find($id);
        if(!$newsletter){
            exit(json_encode(array('suc' => false, 'message' => 'Newsletter not found')));
        }
        
        $recUsers = array();
        switch($recipients){
            case 'ACTIVATED_SHOPS':
                $shops = Model_Shop::getActivated();
                foreach($shops as $shop){
                    $recUsers = $shop->getUser();
                }
                break;
            case 'ALL_SHOPS':
                $shops = Model_Shop::getAll();
                foreach($shops as $shop){
                    $recUsers[] = $shop->getUser();
                }
                break;
            case 'ALL_CUSTOMERS':
                $users = Model_User::getCustomers();
                foreach($users as $user){
                    $recUsers[] = $user;
                }
                break;
            case 'SINGLE_ADDRESS':
                $recUsers[] = new Model_User(array('firstname' => 'testVorname', 'name' => 'testNachname', 'email' => $request->getParam('testmail')));
                break;
            case 'ALL':
                $users = Model_User::getAll();
                foreach($users as $user){
                    $recUsers[] = $user;
                }
                break;
            default:
                exit(json_encode(array('suc' => false, 'message' => 'Invalid recipients')));
                break;
        }

        $suc = $fail = 0;

        if(empty($recUsers)){
            exit(json_encode(array('suc' => false, 'sentSuc' => $suc, 'sentFail' => $fail, 'message' => 'Keine passenden Empfänger gefunden')));
        }

        foreach($recUsers as $user){
            $success = false;
            $replace = array();

            $mail = new Zend_Mail('UTF-8');
            $content = str_replace(array('#firstname#', '#lastname#'), array($user->firstname, $user->name), $newsletter->content);
            if($newsletter->type != 'text') $mail->setBodyHtml($content);
            if($newsletter->type != 'html') $mail->setBodyText(strip_tags($content));
            $mail->setFrom('mail@epelia.com', 'Epelia');
            $mail->addTo($user->email);
            $mail->setSubject($newsletter->subject);
            try{
                $mail->send();
                $suc++;
                $success = true;
            } catch(Exception $e){
                $fail++;
            }
            $newsletter->writeLog($user->email, $success);
        }
        exit(json_encode(array('suc' => true, 'sentSuc' => $suc, 'sentFail' => $fail, 'message' => '')));
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $newsletter = Model_Newsletter::find($id);
        $newsletter->delete();
        $this->_helper->redirector('index');
    }
}
