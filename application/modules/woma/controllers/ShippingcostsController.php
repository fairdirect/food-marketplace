<?php

class Woma_ShippingCostsController extends Zend_Controller_Action{

    public function init(){

        $this->user = Model_User::find(Zend_Auth::getInstance()->getIdentity()->id);
    }

    public function indexAction(){
        Model_User::refreshAuth(); // make sure our data is up to date

        $form = new Woma_Form_WomaShippingCosts();
        $request = $this->getRequest();

        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $woma = $this->user->getWoma();
                Model_WomaShippingCost::deleteForWoma($woma->id);
                $countries = Model_Country::getAll();
                foreach($countries as $country){
                    $shippingCost = new Model_WomaShippingCost(array('woma_id' => $woma->id, 'country_id' => $country->id));
                    $shippingCost->value = str_replace(',', '.', $this->getRequest()->getPost('value_' . $country->id));
                    if(!$shippingCost->value){
                        continue;
                    }
                    if($this->getRequest()->getPost('free_from_' . $country->id)){
                        $shippingCost->free_from = str_replace(',', '.', $this->getRequest()->getPost('free_from_' . $country->id));
                    }
                    try{
                        $shippingCost->save();
                        $this->_redirect('/woma/');
                    } catch(Exception $e){
                    }
                }
            }
        }
        else{
            $shippingCosts = $this->user->getWoma()->getShippingCosts();
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
