<?php

class Admin_ProductratingsController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Produkt-Bewertungen");          
        $this->view->ratings = Model_ProductRating::getAll();
    }

    public function editAction(){
        $form = new Admin_Form_Ratings();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $rating = new Model_ProductRating($request->getPost());
                $rating->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
               $rating = Model_ProductRating::find($id);
                if($rating){
                    $form->populate($rating->toArray());
                }
            }
        }

        $this->view->form = $form;
        $this->view->rating = $rating->rating;
    }

    public function approveAction(){
        $id = $this->getRequest()->getParam('id');
        $rating = Model_ProductRating::find($id);
        if(!$rating){
            exit('Bewertung nicht gefunden!');
        }
        if(!$rating->status == 'accepted'){
            exit('Bewertung bereits freigeschaltet!');
        }
        $rating->status = 'accepted';
        $rating->save();
        $this->_helper->redirector('index');
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $rating = Model_ProductRating::find($id);
        if(!$rating){
            exit('Bewertung nicht gefunden!');
        }
        $rating->delete();
        $this->_helper->redirector('index');
    }

}

