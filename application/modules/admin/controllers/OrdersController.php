<?php

class Admin_OrdersController extends Zend_Controller_Action
{
    public function init(){
    }

    public function indexAction(){
        $this->view->headTitle("Bestellungs-Verwaltung (offen)");          
        $this->view->orderedCarts = Model_ShoppingCart::findByStatus('ordered');
    }


    public function setpaidAction(){
        $id = $this->getRequest()->getParam('id');
        $cart = Model_ShoppingCart::find($id);
        try{
            $shopsWithOrderedProducts = $cart->getShopsWithOrderedProducts();
            $counter = 0;
            foreach($shopsWithOrderedProducts as $shop_id => $shop){
                if(count($shopsWithOrderedProducts) > 1){ // need to add counter to unique order_number
                    $counter++;
                    $order_number = $cart->getCartId() . '-' . $counter;
                }
                else{
                    $order_number = $cart->getCartId();
                }
                $order = new Model_Order(array('user_id' => $cart->user_id, 'shop_id' => $shop->id, 'delivery_addr_id' => $cart->delivery_addr_id, 'billing_addr_id' => $cart->billing_addr_id, 'status' => 'in_process', 'order_number' => $order_number));
                $order->save();

                $orderContent = '';
                foreach($shop->orderedProducts as $pr){                    
                    $order->addProduct($pr['product_id'], $pr['product_name'], $pr['value'], $pr['quantity'], $pr['unit_type'], $pr['content_type'], $pr['contents'], $pr['price_quantity'], $pr['tax']);
                    $orderContent .= $pr['quantity'] . " x " . $pr['product_name'] . " (" . $pr['price_quantity'] . " " . $pr['unit_type'] . " a " . $pr['contents'] . " " . $pr['content_type'] . ")\n\n";
                }            
                $order->insertProducts();
                $order->status = 'in_process';
                $order->shipping = $cart->getShippingCostsForShop($shop_id);
                $order->save();

                $orderMail = Model_Email::find('orderShop');
                $mail = new Zend_Mail('UTF-8');
                
                $mail->setBodyText(strip_tags(str_replace(array('#orderContent#', '#orderNumber#'), array($orderContent, $order->order_number), $orderMail->content)));
                $mail->setFrom('mail@fairdirect.org', 'Sachspendenbörse');
                $mail->addTo($shop->getUser()->email);
                $mail->setSubject(str_replace('#orderNumber#', $order->order_number, $orderMail->subject));
                $mail->send();

                $paymentMail = Model_Email::find('paymentCustomer');
                $mail = new Zend_Mail('UTF-8');
                
                $mail->setBodyText(str_replace(
                    array(
                        '#orderContent#', 
                        '#orderNumber#',
                        '#firstname#',
                        '#lastname#',
                        '#value#'
                    ), 
                    array(
                        $orderContent, 
                        $order->order_number,
                        $order->getBillingAddress()->firstname,
                        $order->getBillingAddress()->name,
                        $order->getPriceTotal()
                    ), 
                    $paymentMail->content
                ));
                $mail->setFrom('mail@fairdirect.org', 'Sachspendenbörse');
                $mail->addTo($order->getUser()->email);
                $mail->setSubject(str_replace('#orderNumber#', $order->order_number, $paymentMail->subject));
                $mail->send();
            }
        } catch(Exception $e){
        echo $e->getMessage();
            exit('Es ist ein Fehler aufgetreten!');
        }
        $cart->status = 'accepted';
        try{
            $cart->save();
        } catch(Exception $e){
            exit('Es ist ein Fehler aufgetreten!');
        }

        $this->_helper->redirector('index');
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $cart = Model_ShoppingCart::find($id);
        $cart->delete();
        $this->_helper->redirector('index');
    }
}
