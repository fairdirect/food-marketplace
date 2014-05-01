<?php

class Woma_WomaController extends Zend_Controller_Action{

    public function init(){

        $this->user = Model_User::find(Zend_Auth::getInstance()->getIdentity()->id);
    }

    public function indexAction(){
        Model_User::refreshAuth(); // make sure our data is up to date

        $form = new Woma_Form_Womas();
        $request = $this->getRequest();

        if($request->getParam('Speichern')){
            if(array_key_exists('id', $request->getPost())
                || array_key_exists('user_id', $request->getPost())
                || array_key_exists('provision', $request->getPost())
            ){
                exit('Forbidden!'); // prevent user from writing forbidden fields
            }
            if($form->isValid($request->getPost())){
                $woma = $this->user->getWoma();
                if(!$woma){
                    $woma = new Model_Woma();
                }
                $woma->init(array_merge(array('user_id' => $this->user->id), $request->getPost()));
                try{
                    $woma->save();
                    $this->_redirect('/woma/');
                } catch(Exception $e){
                }
            }
        }
        else{
            $woma = $this->user->getWoma();
            if($woma){
                $form->populate($woma->toArray());
            }
        }
        $this->view->form = $form;
    }

    public function picturesAction(){
        $woma = $this->user->getWoma();
        if(!$woma){
            $this->_helper->redirector('index');
        }

        $this->view->woma = $woma;
        if($_FILES){
            $type = $this->getRequest()->getParam('type');
            switch($type){
                case 'logo':
                    $womaField = 'logo_id';
                    break;
                case 'history':
                    $womaField = 'history_picture_id';
                    break;
                case 'procedure':
                    $womaField = 'procedure_picture_id';
                    break;
                default:
                    exit('Type not supported');
                    break;
            }
        
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $filename = $type . '_' . $woma->id;
            $filepath = $_SERVER['DOCUMENT_ROOT'] . Zend_Registry::get('config')->pictureupload->path . Zend_Registry::get('config')->womapictures->dir . '/';

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
                $woma->$womaField = $picture->id;
                $woma->save();
            } catch(Exception $e){
                exit($e->getMessage());
            }

            $ret = '<img style="width:140px;" src="/img/womas/' . $filename . '" alt="" />';
            exit(json_encode(array('data' => $ret)));
        }
    }

    public function deletepictureAction(){
        $type = $this->getRequest()->getPost('type');
        switch($type){
            case 'logoPicture':
                $womaField = 'logo_id';
                break;
            case 'history':
                $womaField = 'history_picture_id';
                break;
            case 'procedure':
                $womaField = 'procedure_picture_id';
                break;
            default:
                exit(json_encode(array('suc' => false, 'msg' => 'Type not supported')));
                break;
        }
        if(!$this->user->getWoma()->$womaField){
            exit(json_encode(array('suc' => false, 'msg' => 'Picture not found')));
        }
        
        $picture = Model_Picture::find($this->user->getWoma()->$womaField);
        if(!$picture){
            exit(json_encode(array('suc' => false, 'msg' => 'Picture not found')));
        }
        $picture->delete(); // $type is set to null by contraint
        exit(json_encode(array('suc' => true)));
    }

}
