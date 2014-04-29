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
                htmlspecialchars($woma->taxnumber),
                htmlspecialchars($woma->salestax_id),
                '<a href="/admin/womas/edit/id/' . htmlspecialchars($woma->id) . '/">Editieren</a>'
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

}

