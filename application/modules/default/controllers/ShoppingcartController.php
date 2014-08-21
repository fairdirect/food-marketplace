<?php

class ShoppingcartController extends Zend_Controller_Action
{
    public function init(){
        Model_User::refreshAuth(); // make sure our data is up to date
        $this->auth = Zend_Auth::getInstance();
        $this->user = $this->auth->getIdentity();
        if(!$this->auth->hasIdentity()){
            $session = new Zend_Session_Namespace('Default');
            $session->redirect = '/shoppingcart/';
            $this->_redirect('/login/');
        }
        
        $this->view->hideShoppingCart = true;
    }
    
    public function indexAction(){
        $cartProducts = Model_ShoppingCart::getRunningShoppingCart()->getProducts();
        if(!$cartProducts){
            $this->_redirect('/');
        }
        $products = array();
        $total = 0;
        foreach($cartProducts as $item){
            $product = Model_Product::find($item['product_id']);
            $price = Model_ProductPrice::find($item['product_price_id']);
            $products[$product->shop_id][] = array('product' => $product, 'product_price' => $price, 'quantity' => $item['quantity']);
            $total += $price->value * $item['quantity'];
        }
        $this->view->cart = Model_ShoppingCart::getRunningShoppingCart();
        $this->view->products = $products;
        $this->view->total = $total;
        $this->view->shippingCosts = Model_ShoppingCart::getRunningShoppingCart()->getShippingCosts();
        $this->view->taxValues = Model_ShoppingCart::getRunningShoppingCart()->getValueForTaxes();
        $this->view->shippingTax = Model_ShoppingCart::getRunningShoppingCart()->getShippingTax();
    }

    public function addressesAction(){
        $cartProducts = Model_ShoppingCart::getRunningShoppingCart()->getProducts();
        if(!$cartProducts){
            $this->_redirect('/');
        }
        $session = new Zend_Session_Namespace('Default');
        $session->redirect = '/shoppingcart/addresses/';

        $this->view->user = $this->user;
        $this->view->addresses = $this->auth->getIdentity()->getAddresses();

        if($this->user->main_delivery_address_id && !$session->choosenDeliveryAddress){ 
            $session->choosenDeliveryAddress = $this->user->main_delivery_address_id;
        }
        if($this->user->main_billing_address_id && !$session->choosenBillingAddress){
            $session->choosenBillingAddress = $this->user->main_billing_address_id;
        }

        if($session->choosenDeliveryAddress){
            $this->view->choosenDeliveryAddress = Model_Address::find($session->choosenDeliveryAddress);
        }
        if($session->choosenBillingAddress){
            $this->view->choosenBillingAddress = Model_Address::find($session->choosenBillingAddress);
        }

        if(isset($session->cantDeliver) && is_array($session->cantDeliver) && !empty($session->cantDeliver)){
            $this->view->cantDeliver = $session->cantDeliver;
        }
    }

    public function ajaxsetaddressAction(){
        $session = new Zend_Session_Namespace('Default');
        $type = $this->getRequest()->getPost('type');
        if(!$type){
            exit(json_encode(array('suc' => false, 'msg' => 'No type specified')));
        }
        $address_id = $this->getRequest()->getPost('id');
        if(!$address_id){
            exit(json_encode(array('suc' => false, 'msg' => 'No id specified')));
        }
        $address = Model_Address::find($address_id);
        if(!$address){
            exit(json_encode(array('suc' => false, 'msg' => 'Address not found')));
        }
        if($address->user_id != $this->user->id){
            exit(json_encode(array('suc' => false, 'msg' => 'Forbidden!')));
        }
        switch($type){
            case 'delivery':
                $session->choosenDeliveryAddress = $address->id;
                break;
            case 'billing':
                $session->choosenBillingAddress = $address->id;
                break;
            default:
                exit(json_encode(array('suc' => false, 'msg' => 'Incorrect type')));
                break;
        }
        exit(json_encode(array('suc' => true, 'address' => $this->view->partial('partials/address.phtml', array('address' => $address)))));
    }

    public function ajaxcheckaddressesAction(){
        $session = new Zend_Session_Namespace('Default');

        if(!$session->choosenDeliveryAddress){ // user hat not choosen
            if($this->user->getMainDeliveryAddress()){ // lets see if he wants the default address
                $session->choosenDeliveryAddress = $this->user->getMainDeliveryAddress(); // set default as choosen
            }
            else{ // no address choosen and no default address set
                exit(json_encode(array('suc' => false)));
            }
        }
        if(!$session->choosenBillingAddress){ // user hat not choosen
            if($this->user->getMainBillingAddress()){ // lets see if he wants the default address
                $session->choosenBillingAddress = $this->user->getMainBillingAddress(); // set default as choosen
            }
            else{ // no address choosen and no default address set
                exit(json_encode(array('suc' => false)));
            }
        }
   
        exit(json_encode(array('suc' => true)));
    }



    public function paymentAction(){
        $session = new Zend_Session_Namespace('Default');
        $cartProducts = Model_ShoppingCart::getRunningShoppingCart()->getProducts();
            
        if(!$cartProducts){
            $this->_redirect('/');
        }
        if(!$session->choosenDeliveryAddress){ // user hat not choosen
            if($this->user->getMainDeliveryAddress()){ // lets see if he wants the default address
                $session->choosenDeliveryAddress = $this->user->getMainDeliveryAddress(); // set default as choosen
            }
            else{ // no address choosen and no default address set
                $this->_redirect('/shoppingcart/addresses'); 
            }
        }
        if(!$session->choosenBillingAddress){ // user hat not choosen
            if($this->user->getMainBillingAddress()){ // lets see if he wants the default address
                $session->choosenBillingAddress = $this->user->getMainBillingAddress(); // set default as choosen
            }
            else{ // no address choosen and no default address set
                $this->_redirect('/shoppingcart/addresses'); 
            }
        }
        Model_ShoppingCart::getRunningShoppingCart()->delivery_addr_id = $session->choosenDeliveryAddress;
        Model_ShoppingCart::getRunningShoppingCart()->billing_addr_id = $session->choosenBillingAddress;

        $deliveryAddress = Model_Address::find($session->choosenDeliveryAddress);
        $session->cantDeliver = array();
        foreach($cartProducts as $p){
            $canDeliver = false;
            $product = Model_Product::find($p['product_id']);
            foreach($product->getShop()->getShippingCosts() as $shipping){
                if($shipping->country_id == $deliveryAddress->country){
                    $canDeliver = true;
                    continue;
                }
            }
            if(!$canDeliver){
                $session->cantDeliver[] = $product;
            }
        }
        if(!empty($session->cantDeliver)){
            $this->_redirect('/shoppingcart/addresses');
        }
   
        $this->view->user = $this->user;
    }

    public function checkoutAction(){
        $session = new Zend_Session_Namespace('Default');

        $cartProducts = Model_ShoppingCart::getRunningShoppingCart()->getProducts();
        if(!$cartProducts){
            $this->_redirect('/');
        }
        if(!$session->choosenDeliveryAddress || !$session->choosenBillingAddress){
            $this->_redirect('/shoppingcart/addresses');
        }
        $paymentType = $this->getRequest()->getParam('paymenttype');
        if(!$paymentType){
            $this->_redirect('/shoppingcart/payment/');
        }
        else{
            switch($paymentType){
                case 'prepayment':
                    $this->view->paymentType = 'Vorkasse';
                    break;
                case 'directtransfer':
                    $this->view->paymentType = 'Sofortüberweisung';
                    break;
                default:{
                    exit();
                }
            }
            $session = new Zend_Session_Namespace('Default');
            $session->paymentType = $paymentType;
        }

        $cartProducts = Model_ShoppingCart::getRunningShoppingCart()->getProducts();
        $products = array();
        $total = 0;
        foreach($cartProducts as $item){
            $product = Model_Product::find($item['product_id']);
            $price = Model_ProductPrice::find($item['product_price_id']);
            $products[$product->shop_id][] = array('product' => $product, 'product_price' => $price, 'quantity' => $item['quantity']);
            $total += $price->value * $item['quantity'];
        }

        $this->view->deliveryAddress = Model_Address::find($session->choosenDeliveryAddress);
        $this->view->billingAddress = Model_Address::find($session->choosenBillingAddress);
        $this->view->products = $products;
        $this->view->shippingCosts = Model_ShoppingCart::getRunningShoppingCart()->getShippingCosts();
        $this->view->total = $total;
        $this->view->taxValues = Model_ShoppingCart::getRunningShoppingCart()->getValueForTaxes();
        $this->view->shippingTax = Model_ShoppingCart::getRunningShoppingCart()->getShippingTax();
    }

    public function concludeAction(){
        $cartProducts = Model_ShoppingCart::getRunningShoppingCart()->getProducts();
        if(!$cartProducts){
            $this->_redirect('/');
        }
        $session = new Zend_Session_Namespace('Default');
        $paymentType = $session->paymentType;
        if(!$session->paymentType){
            $this->_redirect('/shoppingcart/payment/');
        }
        $cart = Model_ShoppingCart::getRunningShoppingCart();
        $cart->payment_type = $paymentType;
        $cart->status = 'running';
        $cart->ip = $this->getRequest()->getServer('REMOTE_ADDR');

        $failedItems = $cart->concludeOrder();
        if(!empty($failedItems)){ // TODO: save failed in session and show
            $this->_redirect('/shoppingcart/checkout/paymenttype/' . $session->paymentType . '/');
        }

        $session->payment_type = false;

        $concludeMail = Model_Email::find('orderCustomer');
        $mail = new Zend_Mail('UTF-8');
        $orderContent = '';
        foreach($cart->getProducts() as $pr){
            $unit_type = $pr['unit_type'];
            $content_type = $pr['content_type'];
            $orderContent .= $pr['quantity'] . " x " . $pr['product_name'] . " (" . $pr['quantity'] . " " . $pr['unit_type'] . " a " . $pr['contents'] . " " . $pr['content_type'] . ")\n\n";
        }            

        $content = str_replace(
            array(
                '#orderNumber#', 
                '#orderDate#',
                '#firstname#',
                '#lastname#',
                '#orderContent#',
                '#deliveryAddress#',
                '#billingAddress#',
                '#payment#',
                '#shopContent#'
            ),
            array(
                $cart->id,
                date('d.m.Y', time()),
                $cart->getBillingAddress()->firstname,
                $cart->getBillingAddress()->name,
                $orderContent,
                $cart->getDeliveryAddress()->toMailFormatedString(),
                $cart->getBillingAddress()->toMailFormatedString(),
                'Bitte überweisen Sie den Betrag in Höhe von ' . number_format($cart->getPriceTotal(), 2, ',', '.') . ' EUR auf folgendes Konto:

                Empfänger: Epelia Warenhandel Gattinger
                Kto. Nr.: 4647483
                Bank: Volksbank Heuchelheim
                BLZ: 51361021
                IBAN: DE24513610210004647483
                BIC: GENODE51HHE

                Betreff: Bestellnummer ' . $cart->id
            ),
            $concludeMail->content
        );

        $mail->setBodyText(strip_tags($content));
        $mail->setFrom('mail@epelia.com', 'Epelia');
        $mail->addTo($this->user->email);
        $mail->setSubject(str_replace('#orderNumber#', $cart->id, $concludeMail->subject));
        $mail->send();

        if($cart->payment_type == 'directtransfer'){
            $url = $this->redirectDirecttransfer($cart->id);
            $this->_helper->redirector->gotoUrlAndExit($url);
        }
        else{
            $this->_redirect('/shoppingcart/success/cartid/' . $cart->id);
        }
    }


    private function redirectDirecttransfer($cartId){
        $cart = Model_ShoppingCart::find($cartId);
        if(!$cart){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        if($cart->user_id != $this->user->id){
            exit('Forbidden!');
        }

        // taken from old software, rewrite it.
        /*
        $values = array(
            "24826", // user_id
            "72078", // project_id
            "", // sender_holder; originally "Max Mustermann", probably for tests
            "", // sender_account_number; originally "12345467", probably for tests
            "", // sender_bank_code; originally "88888888", which is for test-only 
                                  // transactions from German sender accounts
            "DE", // sender_country_id; pre-selected as it's the most common, but the 
                                  // customer  can still change it in the form on the sofortueberweisung.de page.
            number_format($sales_price,2), // amount
            "EUR", // currency_id (Pfilchtfeld bei Hashberechnung)
            "Bestellnr.: ".$code, // reason_1
            "Epelia", // reason_2
            "", // user_variable_0
            "", // user_variable_1
            "", // user_variable_2
            "", // user_variable_3
            "", // user_variable_4
            "" // user_variable_5
        );


        $params = array(
            "user_id",
            "project_id",
            "sender_holder",
            "sender_account_number",
            "sender_bank_code",
            "sender_country_id",
            "amount",
            "currency_id",
            "reason_1",
            "reason_2",
            "user_variable_0",
            "user_variable_1",
            "user_variable_2",
            "user_variable_3",
            "user_variable_4",
            "user_variable_5"
        );
        */

        $data = array(
            'user_id' => '24826',
            'project_id' => '72078',
            'sender_holder' => '',
            'sender_account_number' => '',
            'sender_bank_code' => '',
            'sender_country_id' => Model_Address::find($cart->billing_addr_id)->country,
            'amount' => number_format($cart->getPriceTotal(), 2),
            'currency_id' => 'EUR',
            'reason_1' => 'Bestellnr.: '. $cart->id,
            'reason_2' => 'Epelia',
            "user_variable_0" => '',   
            "user_variable_1" => '',
            "user_variable_2" => '',
            "user_variable_3" => '',
            "user_variable_4" => '',
            "user_variable_5" => ''

        );    
        $project_password = "z2IP{zcFZB*_}M~U5Q/|";
        $hash   = md5(implode("|",$data)."|".$project_password);
        $ar     = array();
        foreach($data as $key => $val){
            $ar[] = $key . '=' . $val;
        }
        $url    = "https://www.sofortueberweisung.de/payment/start?".implode("&",$ar)."&hash=".$hash;
        return $url;
        //$url  = "/cart/success.html";
    }


    public function successAction(){
        if(!$this->getRequest()->getParam('cartid')){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        $cart = Model_ShoppingCart::find($this->getRequest()->getParam('cartid'));
        if(!$cart){
            throw new Zend_Controller_Action_Exception('This page does not exist', 404);
        }
        if($cart->user_id != $this->user->id){
            exit('Forbidden!');
        }

        $cartProducts = $cart->getProducts();
        $total = 0;
        foreach($cartProducts as $item){
            $total += $item['value'] * $item['quantity'];
        }
        $total += $cart->getShippingCosts();
        $this->view->cart = $cart;
        $this->view->total = $total;
        switch($cart->payment_type){
            case 'prepayment':
                $this->view->payment = 'Vorkasse';
                break;
            case 'directtransfer':
                $this->view->payment = 'Sofortüberweisung';
                break;
        }
    }
}
