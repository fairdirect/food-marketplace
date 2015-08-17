<?php

class Admin_CategoriesController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout->setLayout('layout_admin');
    }

    public function indexAction()
    {
        $this->view->headTitle("Verwaltung");
           
        $this->view->cats = Model_ProductCategory::findAll();
    }

    public function editAction(){
        $form = new Admin_Form_Categories();

        $request = $this->getRequest();
        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                $category = new Model_ProductCategory($request->getPost());
                $category->save();
                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
               $cat = Model_ProductCategory::find($id);

               $group = $cat->getProductGroup();
               $mainCat = Model_MainCategory::find($group->main_category);
               $typeGroups = $mainCat->getGroups(false, false, false, false);
               $groupElements = array();
               foreach($typeGroups as $gr){
                   $groupElements[$gr->id] = $gr->name;
               }
               $form->getElement('product_group_id')->addMultiOptions($groupElements);
               $formData = array_merge($cat->toArray(), array('type' => $mainCat->id, 'group_id' => $group->id));
               $form->populate($formData);
            }
        }

        $this->view->form = $form;
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $cat = Model_ProductCategory::find($id);
        $cat->delete();
        $this->_helper->redirector('index');
    }


}

