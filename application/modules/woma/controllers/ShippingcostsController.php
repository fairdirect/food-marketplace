<?php

class Business_ShippingCostsController extends Zend_Controller_Action{

    public function init(){

        $this->user = Model_User::find(Zend_Auth::getInstance()->getIdentity()->id);
    }

    public function indexAction(){
        Model_User::refreshAuth(); // make sure our data is up to date

        $form = new Business_Form_ShippingCosts();
        $request = $this->getRequest();

        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $shop = $this->user->getShop();
                Model_ShippingCost::deleteForShop($shop->id);
                $countries = Model_Country::getAll();
                foreach($countries as $country){
                    $shippingCost = new Model_ShippingCost(array('shop_id' => $shop->id, 'country_id' => $country->id));
                    $shippingCost->value = str_replace(',', '.', $this->getRequest()->getPost('value_' . $country->id));
                    if(!$shippingCost->value){
                        continue;
                    }
                    if($this->getRequest()->getPost('free_from_' . $country->id)){
                        $shippingCost->free_from = str_replace(',', '.', $this->getRequest()->getPost('free_from_' . $country->id));
                    }
                    try{
                        $shippingCost->save();
                        $this->_redirect('/business/');
                    } catch(Exception $e){

                    }
                }
            }
        }
        else{
            $shippingCosts = $this->user->getShop()->getShippingCosts();
            $formArray = array();
            foreach($shippingCosts as $shippingCost){
                $formArray['value_' . $shippingCost->country_id] = $shippingCost->value;
                $formArray['free_from_' . $shippingCost->country_id] = $shippingCost->free_from;
            }
            $form->populate($formArray);
        }
        $this->view->form = $form;
    }

}
