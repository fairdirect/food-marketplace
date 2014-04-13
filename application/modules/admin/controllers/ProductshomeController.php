<?php

class Admin_ProductshomeController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");         
        $this->view->homegroups = Model_Homegroup::findAll();
    }

    public function editAction(){
        $form = new Admin_Form_Homegroups();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $homegroup = new Model_Homegroup($request->getPost());
                $homegroup->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
               $homegroup = Model_Homegroup::find($id);
                if($homegroup){
                    $form->populate($homegroup->toArray());
                }
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $homegroup = Model_Homegroup::find($id);
        $homegroup->delete();
        $this->_helper->redirector('index');
    }

    public function bygroupAction(){
        $id = $this->getRequest()->getParam('id');
        $homegroup = Model_Homegroup::find($id);
        $this->view->headTitle("Produkte für Gruppe " . $homegroup->name);
        $this->view->homegroup = $homegroup;
        $this->view->products = $homegroup->getProducts();
    }

    public function addproductAction(){
        $product_id = $this->getRequest()->getParam('product_id');
        $group_id = $this->getRequest()->getParam('group_id');
        $group = Model_Homegroup::find($group_id);
        $group->addProduct($product_id);
        $this->_redirect('/admin/productshome/bygroup/id/' . $group_id);
    }

    public function deleteproductAction(){
        $product_id = $this->getRequest()->getParam('product_id');
        $group_id = $this->getRequest()->getParam('group_id');
        $group = Model_Homegroup::find($group_id);
        $group->deleteProduct($product_id);
        $this->_redirect('/admin/productshome/bygroup/id/' . $group_id);
    }

}

