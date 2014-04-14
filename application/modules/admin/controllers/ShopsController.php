<?php

class Admin_ShopsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Shop-Verwaltung");         
    }

    public function ajaxgetshopsAction(){
        $shops = Model_Shop::getAll($this->getRequest()->getParam('iDisplayLength'), $this->getRequest()->getParam('iDisplayStart'), $this->getRequest()->getParam('sSearch'));
        $ret = array(
            'sEcho' => $this->getRequest()->getParam('sEcho') + 1,
            'iTotalRecords' => Model_Shop::getCount(),
            'iTotalDisplayRecords' => count($shops),
            'aaData' => array()
        );
        foreach($shops as $shop){
            $ret['aaData'][] = array(
                htmlspecialchars($shop->id),
                '<a href="/admin/users/edit/id/' . htmlspecialchars($shop->getUser()->id) . '/">' . htmlspecialchars($shop->getUser()->email) . '</a>',
                htmlspecialchars($shop->name),
                htmlspecialchars($shop->url),
                htmlspecialchars($shop->provision),
                htmlspecialchars($shop->taxnumber),
                htmlspecialchars($shop->salestax_id),
                '<a href="/admin/shops/edit/id/' . htmlspecialchars($shop->id) . '/">Editieren</a>'
            );
        }
        exit(json_encode($ret));
    }

    public function editAction(){
        $form = new Admin_Form_Shops();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $shop = new Model_Shop($request->getPost());
                try{
                    $shop->save();

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
                        $this->view->error = 'Nutzer mit ID ' . $shop->user_id . ' nicht gefunden.';
                    }
                    else{
                        $this->view->error = $e->getMessage();
                    }
                }
            }
        }
        else{
            if($id){
               $shop = Model_Shop::find($id);
                if($shop){
                    $form->populate($shop->toArray());
                }
            }
        }
        $this->view->form = $form;
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $shop = Model_Shop::find($id);
        $shop->delete();
        $this->_helper->redirector('index');
    }

}

