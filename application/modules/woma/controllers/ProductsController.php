<?php

class Woma_ProductsController extends Zend_Controller_Action
{

    public function init(){
        Model_User::refreshAuth(); // make sure our data is up to date
        $this->user = Zend_Auth::getInstance()->getIdentity();      
    }

    public function indexAction()
    {
        $this->view->headTitle("Produkt-Verwaltung");          
        $this->view->products = $this->user->getWoma()->getProducts(null, null, false, false, false, false);
    }

    public function editAction(){
        $form = new Woma_Form_Products();

        $woma = $this->user->getWoma();
        $shop_options = array();
        foreach($woma->getShops() as $shop){
            $shop_options[$shop->id] = $shop->name;
        }
        $form->getElement('shop_id')->addMultiOptions($shop_options);

        $request = $this->getRequest();

        $attributeTypes = array('additive' => 'Zusätze', 'allergen' => 'Allergene', 'event' => 'Anlässe', 'flavor' => 'Geschmack', 'manipulation' => 'Veränderungen');

        $id = $request->getParam('id');
        if($id){ // editing
            $product = Model_Product::find($id);
            if(!in_array($product->getShop()->id, $this->user->getWoma()->getShopIds())){
                exit('Forbidden!'); // prevent user from writing products not owned by them
            }
        }
        else{ // new product
            $product = new Model_Product(array());
        }

        if($request->getParam('Speichern')){
            if(!in_array($request->getPost('shop_id'), $this->user->getWoma()->getShopIds())){
                exit('Forbidden!'); // prevent user from writing products not owned by them
            }

            if($form->isValid($request->getPost())){
                $product->init($request->getPost());
                if($request->getPost('unlimited_stock')){
                    $product->stock = NULL; // stock === null implies unlimited stock
                }
                else{
                    if(!$request->getPost('stock') && !is_int($request->getPost('stock'))){
                        $product->stock = 0;
                    }
                }
                try{
                     $product->save();
                } catch(Exception $e){
                    echo $e->getMessage();
                    exit();
                }

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
                    $typeGroups = Model_ProductGroup::getByType($group->type, false, false, false, false);
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
                    $formData = array_merge($product->toFormArray(), array('type' => $group->type, 'group_id' => $group->id, 'category_id' => $category->id));

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
        if(!in_array($product->getShop()->id, $this->user->getWoma()->getShopIds())){
            exit('Forbidden!'); // prevent user from writing products not owned by them
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
                exit($e->getMessage());
            }

            $ret = '<tr><td><input type="checkbox" name="imagesDelete[]" value="" /></td><td><img style="width:120px;" src="/img/products/174x136/' . $filename . '" alt=""></td></tr>';
            exit(json_encode(array('data' => $ret)));
        }
    }

    public function deletepictureAction(){
        $picture = Model_Picture::find($this->getRequest()->getPost('id'));
        if(!$picture){
            exit(json_encode(array('suc' => false)));
        }
        $products = $picture->getAssociatedProducts();
        foreach($products as $product){
            if(in_array($product->getShop()->id, $this->user->getWoma()->getShopIds())){ // make sure the picture belongs to the user
                $picture->delete();
                break;
            }
        }
        exit(json_encode(array('suc' => true)));
    }

    public function deleteAction(){
        $id = $this->getRequest()->getParam('id');
        $product = Model_Product::find($id);
        if(!in_array($product->getShop()->id, $this->user->getWoma()->getShopIds())){
            exit('Forbidden!'); // prevent user from writing products not owned by them
        }
        $product->delete();
        $this->_helper->redirector('index');
    }
}
