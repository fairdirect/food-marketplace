<?php

class MeinepeliaController extends Zend_Controller_Action
{

    public function init(){
        Model_User::refreshAuth(); // make sure our data is up to date
        $this->auth = Zend_Auth::getInstance();
        if($this->auth->hasIdentity()){
            $this->user = $this->auth->getIdentity();
        }
        else{
            $this->_redirect('/');
        }
    }

    public function indexAction(){        
        $this->view->headTitle("Home");
    }

    public function ordersAction(){
        $this->view->headTitle('Meine Bestellungen | Epelia');
        $this->view->user = $this->user;
        $this->view->orderedCarts = $this->user->getShoppingCarts('ordered');
        $this->view->orders = $this->user->getOrders();
    }

    public function addressesAction(){
        $this->view->headTitle("Meine Adressen | Epelia");
        $this->view->user = $this->user;
        $this->view->addresses = $this->user->getAddresses();
    }

    public function addresseseditAction(){
        $form = new Form_Addresses();

        $request = $this->getRequest();
        $id = $request->getParam('id');

        if($id){ // editing
            $address = Model_Address::find($id);
            if($address->user_id != $this->user->id){
                exit('Forbidden!'); // prevent user from writing addresses not owned by them
            }
        }
        else{ // new product
            $address = new Model_Address(array('user_id' => $this->user->id));
        }

        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $address->init($request->getPost());
                $address->save();
                Model_User::refreshAuth(); // make sure our data is up to date
                $session = new Zend_Session_Namespace('Default');
                if($session->redirect){
                    $oldRedirect = $session->redirect;
                    $session->redirect = false;
                    $this->_redirect($oldRedirect);
                }
                else{
                    $this->_redirect('/meinepelia/addresses/');
                }
            }
        }
        else{
            if($id){
               $address = Model_Address::find($id);
                if($address){
                    $form->populate($address->toArray());
                }
            }
            else{
                $form->populate(array('user_id' => $request->getParam('user_id')));
            }
        }

        $this->view->form = $form;
    }

    public function setdefaultdeliveryAction(){
        $request = $this->getRequest();
        $address = Model_Address::find($request->getParam('addressid'));
        if($address && $this->user->id == $address->user_id){
            $this->user->main_delivery_address_id = $address->id;
            $this->user->save();
        }
        $session = new Zend_Session_Namespace('Default');
        if($session->redirect){
            $oldRedirect = $session->redirect;
            $session->redirect = false;
            $this->_redirect($oldRedirect);
        }
        else{
            $this->_redirect('/meinepelia/addresses/');
        }
    }

    public function setdefaultbillingAction(){
        $request = $this->getRequest();
        $address = Model_Address::find($request->getParam('addressid'));
        if($address && $this->user->id == $address->user_id){
            $this->user->main_billing_address_id = $address->id;
            $this->user->save();
        }
        $session = new Zend_Session_Namespace('Default');
        if($session->redirect){
            $oldRedirect = $session->redirect;
            $session->redirect = false;
            $this->_redirect($oldRedirect);
        }
        else{
            $this->_redirect('/meinepelia/addresses/');
        }
    }

    public function productratingsAction(){
        $this->view->headTitle("Meine Produkt-Bewertungen | Epelia");
        $unratedProducts = $ratedProductIds = array();
        $ratings = $this->user->getProductRatings();
        foreach($ratings as $rating){
            if(!in_array($rating->product_id, $ratedProductIds)){
                $ratedProductIds[] = $rating->product_id;
            }
        }
        $orders = $this->user->getOrders();
        foreach($orders as $order){
            $items = $order->getProducts();
            foreach($items as $item){
                if(!in_array($item->product_id, $ratedProductIds)){
                    $unratedProducts[] = $item;
                }
            }
        }

        $this->view->user = $this->user;
        $this->view->productRatings = $ratings;
        $this->view->unratedProducts = $unratedProducts;
    }

    public function createratingAction(){
        $this->view->headTitle("Neue Produkt-Bewertung anlegen | Epelia");

        $unratedProductIds = $ratedProductIds = array();
        $ratings = $this->user->getProductRatings();
        foreach($ratings as $rating){
            if(!in_array($rating->product_id, $ratedProductIds)){
                $ratedProductIds[] = $rating->product_id;
            }
        }
        $orders = $this->user->getOrders();
        foreach($orders as $order){
            $items = $order->getProducts();
            foreach($items as $item){
                if(!in_array($item->product_id, $ratedProductIds)){
                    $unratedProductIds[] = $item->product_id;
                }
            }
        }

        $id = $this->getRequest()->getParam('productid');
        if(!$id || !in_array($id, $unratedProductIds)){
            exit('Forbidden!'); // prevent user from rating products not ordered by them
        }

        $form = new Form_Ratings();

        if($this->getRequest()->getParam('Speichern')){
            if($form->isValid($this->getRequest()->getPost())){
                $rating = new Model_ProductRating($form->getValues());
                if($rating->rating < 1 || $rating->rating > 5){ // double check
                    exit('Wert muss zwischen 1 und 5 liegen');
                }
                $rating->save();
                Model_User::refreshAuth(); // make sure our data is up to date
                $session = new Zend_Session_Namespace('Default');
                $this->_redirect('/meinepelia/productratings/');             
            }
        }
        else{
            $form->populate(array('productid' => $id));
        }

        $product = Model_Product::find($id);
        $this->view->product = $product;

        $this->view->form = $form;
    }

}
