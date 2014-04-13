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
            foreach($shopsWithOrderedProducts as $shop_id => $shop){
                $order = new Model_Order(array('user_id' => $cart->user_id, 'shop_id' => $shop->id, 'delivery_addr_id' => $cart->delivery_addr_id, 'billing_addr_id' => $cart->billing_addr_id, 'status' => 'in_process', 'order_number' => $cart->id));
                $order->save();

                $orderContent = '';
                foreach($shop->orderedProducts as $pr){
                    $product = Model_Product::find($pr['product_id']);
                    $price = Model_ProductPrice::find($pr['product_price_id']);
                    $unit_type = ($pr['quantity'] > 1) ? $price->getUnitType()->plural : $price->getUnitType()->singular;
                    $content_type = $price->getContentType()->name;

                    $order->addProduct($pr['product_id'], $price->value, $pr['quantity'], $unit_type, $content_type, $price->contents, $price->quantity);
                    $orderContent .= $pr['quantity'] . " x " . $product->name . " (" . $price->quantity . " " . (($price->quantity == 1) ? $price->getUnitType()->singular : $price->getUnitType()->plural) . " a " . $price->contents . " " . $price->getContentType()->name . ")\n\n";
                }            
                $order->insertProducts();
                $order->status = 'in_process';
                $order->shipping = $cart->getShippingCostsForShop($shop_id);
                $order->save();

                $orderMail = Model_Email::find('orderShop');
                $mail = new Zend_Mail('UTF-8');
                
                $mail->setBodyText(strip_tags(str_replace(array('#orderContent#', '#orderNumber#'), array($orderContent, $order->order_number), $orderMail->content)));
                $mail->setFrom('mail@epelia.com', 'Epelia');
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
                $mail->setFrom('mail@epelia.com', 'Epelia');
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
