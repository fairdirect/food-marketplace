<?php

class Admin_ProductsController extends Zend_Controller_Action
{
    public function init(){
    }

    public function indexAction()
    {
        $this->view->headTitle("Produkt-Verwaltung");          
    }

    public function ajaxgetproductsAction(){
        $products = Model_Product::getAll($this->getRequest()->getParam('iDisplayLength'), $this->getRequest()->getParam('iDisplayStart'), false, false, false, false, false, $this->getRequest()->getParam('sSearch'));
        $ret = array(
            'sEcho' => $this->getRequest()->getParam('sEcho') + 1,
            'iTotalRecords' => Model_Product::getCount(false, false, false, false, false),
            'iTotalDisplayRecords' => Model_Product::getCount(false, false, false, false, false, $this->getRequest()->getParam('sSearch')),
            'aaData' => array()
        );

        foreach($products as $product){
            $ret['aaData'][] = array(
                htmlspecialchars($product->id),
                htmlspecialchars($product->getShop()->name),
                htmlspecialchars($product->name),
                ($product->active) ? 'ja' : 'nein',
                (is_null($product->stock)) ? 'unbegrenzt' : htmlspecialchars($product->stock),
                date('d.m.Y', strtotime($product->created)),
                '<a href="/admin/products/edit/id/' . htmlspecialchars($product->id) . '">Editieren</a><br /><a href="/admin/products/pictures/id/' . htmlspecialchars($product->id) . '">Bilder</a><br />' . (($product->active) ? '<a href="/admin/products/deactivate/id/' . htmlspecialchars($product->id) . '/" title="deaktivieren">deaktivieren</a>' : '<a href="/admin/products/activate/id/' . htmlspecialchars($product->id) . '/" title="aktivieren">aktivieren</a>' ) . '<br /><a href="/admin/products/delete/id/' . htmlspecialchars($product->id) . '/" title="löschen">löschen</a>'
            );
        }
        exit(json_encode($ret));
    }


    public function editAction(){
        $form = new Admin_Form_Products();
        $request = $this->getRequest();

        $attributeTypes = array('additive' => 'Zusätze', 'allergen' => 'Allergene', 'event' => 'Anlässe', 'flavor' => 'Geschmack', 'manipulation' => 'Veränderungen');

        $id = $request->getParam('id');
        if($request->getParam('Speichern')){
            if($form->isValid($request->getPost())){
                if($id){
                    $product = Model_Product::find($id);
                    $product->init($request->getPost());
                }
                else{
                    $product = new Model_Product($request->getPost());
                }
                if($request->getPost('unlimited_stock')){
                    $product->stock = NULL; // stock === null implies unlimited stock
                }
                else{
                    if(!$request->getPost('stock') && !is_int($request->getPost('stock'))){
                        $product->stock = 0;
                    }
                }

                $product->save();

                $attributes = array();
                Model_ProductAttribute::deleteForProduct($product->id);
                foreach($attributeTypes as $aName => $aLabel){
                    if($request->getPost($aName)){
                        $attrGroup = $request->getPost($aName);
                        foreach($attrGroup as $a){
                            $attributes[] = $a;
                        }
                    }
                }
                $product->setAttributes($attributes);

                Model_ProductPrice::deleteForProduct($product->id);
                $counter = 0;
                while(++$counter <= 3){
                    $price = new Model_ProductPrice(array('product_id' => $product->id));
                    $price->is_wholesale = 'false';
                    $price->quantity = $request->getPost('normal_amount_' . $counter);
                    $price->unit_type_id = $request->getPost('normal_unit_' . $counter);
                    $price->contents = $request->getPost('normal_content_' . $counter);
                    $price->content_type_id = $request->getPost('normal_content_type_' . $counter);
                    $price->value = $request->getPost('normal_euro_' . $counter) + ($request->getPost('normal_cent_' . $counter) / 100);
                    try{
                        $price->save();
                    } catch(Exception $e){
                    
                    }
                }

                $counter = 0;
                while(++$counter <= 3){
                    $price = new Model_ProductPrice(array('product_id' => $product->id));
                    $price->is_wholesale = 'true';
                    $price->quantity = $request->getPost('wholesale_amount_' . $counter);
                    $price->unit_type_id = $request->getPost('wholesale_unit_' . $counter);
                    $price->contents = $request->getPost('wholesale_content_' . $counter);
                    $price->content_type_id = $request->getPost('wholesale_content_type_' . $counter);
                    $price->value = $request->getPost('wholesale_euro_' . $counter) + ($request->getPost('wholesale_cent_' . $counter) / 100);
                    try{
                        $price->save();
                    } catch(Exception $e){

                    }
                }

                $this->_helper->redirector('index');
            }
        }
        else{
            if($id){
                $product = Model_Product::find($id);
                $attributes = $product->getAttributes();
                if($product){
                    $category = $product->getCategory();
                    $group = $category->getProductGroup();
                    $mainCat = Model_MainCategory::find($group->main_category);
                    $typeGroups = $mainCat->getGroups();
                    $groupElements = array();
                    foreach($typeGroups as $gr){
                        $groupElements[$gr->id] = $gr->name;
                    }
                    $form->getElement('group_id')->addMultiOptions($groupElements);
                    $groupCategories = Model_ProductCategory::findByGroup($group->id);
                    $categoryElements = array();
                    foreach($groupCategories as $cat){
                        $categoryElements[$cat->id] = $cat->name;
                    }
                    $form->getElement('category_id')->addMultiOptions($categoryElements);
                    $formData = array_merge($product->toFormArray(), array('type' => $mainCat->id, 'group_id' => $group->id, 'category_id' => $category->id));
                    $form->populate($formData);
                }
            }
        }
        $this->view->form = $form;
    }

    public function picturesAction(){
        $id = $this->getRequest()->getParam('id');
        if(!$id){
            $this->_helper->redirector('index');
        }
        $product = Model_Product::find($id);
        if(!$product){
            $this->_helper->redirector('index');
        }
        $this->view->product = $product;
        if($_FILES){
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $filename = 'products_' . $product->id;
            $filepath = $_SERVER['DOCUMENT_ROOT'] . Zend_Registry::get('config')->pictureupload->path . Zend_Registry::get('config')->productpictures->dir;

            $counter = 0;
            while(file_exists($filepath . $filename . '.' . $extension)){
                $filename = str_replace('_' . ($counter), '', $filename);
                $filename .= '_' . ($counter+1);
                $counter++;
            }
            $filename = $filename . '.' . $extension;
            try{
                $img = new Imagick();

                $img->readImage($_FILES['file']['tmp_name']);
                $img->writeImage($filepath . 'original/' . $filename);
                $img->thumbnailImage(380, null);
                $img->writeImage($filepath . '380w/' . $filename);
                $img->thumbnailImage(380, 380, true);
                $img->writeImage($filepath . '380x285/' . $filename);
                $img->thumbnailImage(174, 174, true);
                $img->writeImage($filepath . '174x136/' . $filename);
                $img->thumbnailImage(90, 90, true);
                $img->writeImage($filepath . '90x68/' . $filename);
                $img->thumbnailImage(36, 36, true);
                $img->writeImage($filepath . '36x27/' . $filename);
               
                $picture = new Model_Picture(array('filename' => $filename));
                $picture->save();
                $product->addPicture($picture->id);
            } catch(Exception $e){
                exit();
            }

            $ret = '<tr><td><input type="checkbox" name="imagesDelete[]" value="" /></td><td><img style="width:120px;" src="/img/products/174x136/' . htmlspecialchars($filename) . '" alt=""></td></tr>';
            exit(json_encode(array('data' => $ret)));
        }
    } 

    public function deletepictureAction(){
        $picture = Model_Picture::find($this->getRequest()->getPost('id'));
        if(!$picture){
            exit(json_encode(array('suc' => false)));
        }
        $picture->delete();
        exit(json_encode(array('suc' => true)));
    }


    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $product = Model_Product::find($id);
        $product->delete();
        $this->_helper->redirector('index');
    }

    public function activateAction(){
        $id = $this->getRequest()->getParam('id');
        $product = Model_Product::find($id);
        $product->active = true;
        $product->save();
        $this->_helper->redirector('index');
    }

    public function deactivateAction(){
        $id = $this->getRequest()->getParam('id');
        $product = Model_Product::find($id);
        $product->active = false;
        $product->save();
        $this->_helper->redirector('index');
    }
}
