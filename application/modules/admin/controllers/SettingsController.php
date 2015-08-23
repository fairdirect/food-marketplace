<?php

class Admin_SettingsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
           
        $this->view->settings = Model_Setting::getAll();
    }

    public function editAction(){
        $form = new Admin_Form_Settings();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $setting = new Model_Setting($request->getPost());
                $setting->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){               
                $setting = Model_Setting::find($id);
                if($setting){
                    $form->populate($setting->toArray());
                }
            }
        }
        $this->view->form = $form;
    }

}
