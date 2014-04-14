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
                '<a href="/admin/users/edit/id/' . htmlspecialchars($woma->getUser()->id) . '/">' . htmlspecialchars($woma->getUser()->email) . '</a>',
                htmlspecialchars($woma->name),
                htmlspecialchars($woma->url),
                htmlspecialchars($woma->provision),
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

                    if($request->getPost('imagesDelete')){
                        foreach($request->getPost('imagesDelete') as $imgId){
                            $img = Marktplatz_Model_ProductImage::getImage($imgId);
                            $img->delete();
                        }
                    }
                    if($request->getPost('imagesOld')){
                        foreach($request->getPost('imagesOld') as $imgId){
                            $product->_productImages[] = Marktplatz_Model_ProductImage::getImage($imgId);
                        }
                    }
                    if($request->getPost('imagesNew')){
                        foreach($request->getPost('imagesNew') as $filename){
                            $product->_productImages[] = new Marktplatz_Model_ProductImage(array(
                                'crdate' => time(),
                                'name' => $product->name,
                                'image' => $filename,
                                'productid' => $product->uid
                            ));
                        }
                    }

                    $this->_helper->redirector('index');
                } catch(Exception $e){
                    if($e->getCode() == 23503){
                        $this->view->error = 'Nutzer mit ID ' . $woma->user_id . ' nicht gefunden.';
                    }
                    else{
                        $this->view->error = $e->getMessage();
                    }
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

