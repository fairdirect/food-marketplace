<?php

class Business_SalesController extends Zend_Controller_Action
{
    public function init(){
        Model_User::refreshAuth(); // make sure our data is up to date
        $this->user = Zend_Auth::getInstance()->getIdentity();      
    }

    public function indexAction(){
        $this->_redirect('/business/');
    }

    public function ordersAction(){
        $this->view->headTitle("Bestellungs-Verwaltung (nicht versendet)");          
        $this->view->inProcessOrders = Model_Order::findByShop($this->user->getShop()->id, 'in_process');
    }

    public function archiveAction(){
        $this->view->headTitle("Bestellungs-Verwaltung (abgeschlossen)");          
        $this->view->completeOrders = Model_Order::findByShop($this->user->getShop()->id, 'complete');
    }

    public function setsendAction(){
        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($id){ 
            $order = Model_Order::find($id);
            if($order->getShop()->user_id != $this->user->id){
                exit('Forbidden!'); // prevent user from writing orders not owned by them
            }
            if($order->status != 'in_process'){
                exit('Already marked as sent');
            }
        }
        else{ 
            $this->_redirect('index');
        }

        try{
            $order->status = 'complete';
            $order->send_date = date('Y-m-d', time());
            $order->save();
        } catch(Exception $e){
            exit('Ein Fehler ist aufgetreten');
        }

        $sentMail = Model_Email::find('ordersent');
        $mail = new Zend_Mail('UTF-8');
        
        $mail->setBodyText(str_replace(
            array(
                '#shopname#',
                '#orderNumber#',
                '#firstname#',
                '#lastname#'
            ), 
            array(
                $order->getShop()->name, 
                $order->order_number,
                $order->getBillingAddress()->firstname,
                $order->getBillingAddress()->name
            ), 
            $sentMail->content
        ));
        $mail->setFrom('mail@epelia.com', 'Epelia');
        $mail->addTo($order->getUser()->email);
        $mail->setSubject(str_replace(array('#orderNumber#', '#shopname#'), array($order->order_number, $order->getShop()->name), $sentMail->subject));
        $mail->send();


        $this->_helper->redirector('orders');
    }

}
