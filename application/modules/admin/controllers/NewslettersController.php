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
                $this->view->newsletter = $newsletter;
            }
        }
        $this->view->form = $form;
        $this->view->sendForm = $sendForm;
    }

    public function ajaxgetrecipientsAction(){
        $recipients = $this->getRequest()->getParam('recipients');
        if(!$recipients){
            exit(json_encode(array('suc' => false, 'message' => 'Recipients are missing')));
        }
        if(!$recipients == 'SINGLE_ADDRESS' && !$request->getParam('testmail')){
            exit(json_encode(array('suc' => false, 'message' => 'Testmail is missing')));
        }

        $recUsers = array();
        switch($recipients){
            case 'ALL_SHOPS':
                $shops = Model_Shop::getAll();
                foreach($shops as $shop){
                    if($shop->getUser() && $shop->getUser()->email){
                        $recUsers[] = $shop->getUser();
                    }
                }
                break;
            case 'ALL_CUSTOMERS':
                $users = Model_User::getCustomers();
                foreach($users as $user){
                    if($user->email){
                       $recUsers[] = $user;
                   }
                }
                break;
            case 'ALL_CUSTOMERS_NEWSLETTER':
                $users = Model_User::getCustomersWithNewsletter();
                foreach($users as $user){
                    if($user->email){
                        $recUsers[] = $user;
                    }
                }
                break;
            case 'SINGLE_ADDRESS':
                $recUsers[] = Model_User::findByEmail($request->getParam('testmail'));
                break;
            case 'ALL':
                $users = Model_User::getAll();
                foreach($users as $user){
                    if($user->email){
                        $recUsers[] = $user;
                    }
                }
                break;
            default:
                exit(json_encode(array('suc' => false, 'message' => 'Invalid recipients')));
                break;
        }
        exit(json_encode(array('suc' => true, 'payload' => $recUsers)));
    }


    public function sendAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        if(!$id){
            exit(json_encode(array('suc' => false, 'message' => 'ID is missing')));
        }
        $newsletter = Model_Newsletter::find($id);
        if(!$newsletter){
            exit(json_encode(array('suc' => false, 'message' => 'Newsletter not found')));
        }
        
        $success = false;
        $replace = array();

        $mail = new Zend_Mail('UTF-8');
        $user = Model_User::findByEmail($request->getParam('recipient'));
        if(!$user){
            exit(json_encode(array('suc' => false, 'message' => 'User mit Mail ' . $request->getParam('recipient') . ' nicht gefunden.')));
        }
        $address = $user->getMainBillingAddress();
        $anrede = '';
        if($address){
            $anrede = $address->gender . ' ' . $address->name;
        }
        $content = str_replace(array('#ANREDE#'), array($anrede), $newsletter->content);
        $subject = str_replace(array('#ANREDE#'), array($anrede), $newsletter->subject);
        if($newsletter->type != 'text') $mail->setBodyHtml($content);
        if($newsletter->type != 'html') $mail->setBodyText(strip_tags($content));
        $mail->setFrom('mail@fairdirect.org', 'OpenFoodBank');
        $mail->addTo($user->email);
        $mail->setSubject($subject);
        try{
            $mail->send();
            $sendSuc = true;
        } catch(Exception $e){
            $sendSuc = false;
        }
        $newsletter->writeLog($user->email, $success);

        exit(json_encode(array('suc' => true, 'sendSuc' => $sendSuc)));
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $newsletter = Model_Newsletter::find($id);
        $newsletter->delete();
        $this->_helper->redirector('index');
    }
}
