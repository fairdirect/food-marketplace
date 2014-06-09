<?php

class Business_ShopController extends Zend_Controller_Action{

    public function init(){

        $this->user = Model_User::find(Zend_Auth::getInstance()->getIdentity()->id);
    }

    public function termsAction(){

    }

    public function indexAction(){
        Model_User::refreshAuth(); // make sure our data is up to date

        $form = new Business_Form_Shops();
        $request = $this->getRequest();

        if($request->getParam('Speichern')){
            if(array_key_exists('id', $request->getPost())
                || array_key_exists('user_id', $request->getPost())
                || array_key_exists('provision', $request->getPost())
            ){
                exit('Forbidden!'); // prevent user from writing forbidden fields
            }
            if($form->isValid($request->getPost())){
                $shop = $this->user->getShop();
                if(!$shop){
                    $shop = new Model_Shop();
                }
                $shop->init(array_merge(array('user_id' => $this->user->id), $request->getPost()));
                if($shop->url){
                    $shop->url = Epelia_Helper::make_url($shop->url);
                }
                else{
                    $shop->url = Epelia_Helper::make_url($shop->name);
                }
                try{
                    $shop->save();
                    $this->_redirect('/business/');
                } catch(Exception $e){
                    if($e->getCode() == 23505){
                        $this->view->error = 'Die URL "' . $shop->url . '" wird bereits verwendet.';
                    }
                }
            }
        }
        else{
            $shop = $this->user->getShop();
            if($shop){
                $form->populate($shop->toArray());
            }
        }
        $this->view->form = $form;
    }

    public function picturesAction(){
        $shop = $this->user->getShop();
        if(!$shop){
            $this->_helper->redirector('index');
        }

        $this->view->shop = $shop;
        if($_FILES){
            $type = $this->getRequest()->getParam('type');
            switch($type){
                case 'logo':
                    $shopField = 'logo_id';
                    break;
                case 'history':
                    $shopField = 'history_picture_id';
                    break;
                case 'procedure':
                    $shopField = 'procedure_picture_id';
                    break;
                default:
                    exit('Type not supported');
                    break;
            }
        
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $filename = $type . '_' . $shop->id;
            $filepath = $_SERVER['DOCUMENT_ROOT'] . Zend_Registry::get('config')->pictureupload->path . Zend_Registry::get('config')->shoppictures->dir . '/';

            $counter = 0;
            while(file_exists($filepath . $filename . '.' . $extension)){
                $filename = str_replace('_' . ($counter), '', $filename);
                $filename .= '_' . ($counter+1);
                $counter++;
            }
            $filename = $filename . '.' . $extension;
            $file = $filepath . $filename;
            try{
                $img = new Imagick();
                $img->readImage($_FILES['file']['tmp_name']);
                $img->thumbnailImage(140, 140, true);
                $img->writeImage($file);
                
                $picture = new Model_Picture(array('filename' => $filename));
                $picture->save();
                $shop->$shopField = $picture->id;
                $shop->save();
            } catch(Exception $e){
                exit($e->getMessage());
            }

            $ret = '<img style="width:140px;" src="/img/shops/' . $filename . '" alt="" />';
            exit(json_encode(array('data' => $ret)));
        }
    }

    public function deletepictureAction(){
        $type = $this->getRequest()->getPost('type');
        switch($type){
            case 'logoPicture':
                $shopField = 'logo_id';
                break;
            case 'history':
                $shopField = 'history_picture_id';
                break;
            case 'procedure':
                $shopField = 'procedure_picture_id';
                break;
            default:
                exit(json_encode(array('suc' => false, 'msg' => 'Type not supported')));
                break;
        }
        if(!$this->user->getShop()->$shopField){
            exit(json_encode(array('suc' => false, 'msg' => 'Picture not found')));
        }
        
        $picture = Model_Picture::find($this->user->getShop()->$shopField);
        if(!$picture){
            exit(json_encode(array('suc' => false, 'msg' => 'Picture not found')));
        }
        $picture->delete(); // $type is set to null by contraint
        exit(json_encode(array('suc' => true)));
    }

}
