<?php

class Admin_WomasController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Wochenmarkt-Verwaltung");         
    }

    public function ajaxgetwomasAction(){
        $womas = Model_Woma::getAll($this->getRequest()->getParam('iDisplayLength'), $this->getRequest()->getParam('iDisplayStart'), $this->getRequest()->getParam('sSearch'));
        $ret = array(
            'sEcho' => $this->getRequest()->getParam('sEcho') + 1,
            'iTotalRecords' => Model_Woma::getCount(),
            'iTotalDisplayRecords' => count($womas),
            'aaData' => array()
        );
        foreach($womas as $woma){
            $ret['aaData'][] = array(
                htmlspecialchars($woma->id),
                htmlspecialchars($woma->name),
                htmlspecialchars($woma->url),
                '<a href="/admin/womas/edit/id/' . htmlspecialchars($woma->id) . '/">Editieren</a><br /><a href="/admin/womas/editshippingcosts/id/' . htmlspecialchars($woma->id) . '/">Versandkosten</a>' 
            );
        }
        exit(json_encode($ret));
    }

    public function editAction(){
        $form = new Admin_Form_Womas();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $woma = new Model_Woma($request->getPost());
                try{
                    $woma->save();
                    $this->_helper->redirector('index');
                } catch(Exception $e){
                    $this->view->error = $e->getMessage();
                }
            }
        }
        else{
            if($id){
               $woma = Model_Woma::find($id);
                if($woma){
                    $form->populate($woma->toArray());
                }
            }
        }
        $this->view->form = $form;
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $woma = Model_Woma::find($id);
        $woma->delete();
        $this->_helper->redirector('index');
    }

    public function editshippingcostsAction(){
        $form = new Admin_Form_WomaShippingCosts();
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $woma = Model_Woma::find($id);

        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
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
                    $shippingCost->save();
                    $this->_redirect('/admin/womas/');
                }
            }
        }
        else{
            $shippingCosts = $woma->getShippingCosts();
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

