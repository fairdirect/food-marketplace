<?php

class Model_Picture extends Model_ModelAbstract
{
    public $id;
    public $filename;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Picture();
    }

    public static function getByProduct($productID){
        $ret = array();
        $db = self::getDbTable()->getAdapter();
        $result = $db->query("SELECT picture_id FROM epelia_products_pictures WHERE product_id = ?", $productID);
        if(is_null($result)){
            return array();
        }
        foreach($result as $r){
            $ret[] = self::find($r);
        }
        return $ret;
    }

    public static function getByShop($shopID){
        $ret = array();
        $db = self::getDbTable()->getAdapter();
        $result = $db->query("SELECT picture_id FROM epelia_shops_pictures WHERE shop_id = ?", $shopID);
        if(is_null($result)){
            return array();
        }
        foreach($result as $r){
            $ret[] = self::find($r);
        }
        return $ret;
    }

    public function delete(){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $this->id);
        $table->delete($where); // by ON CASCADE pictures are removed from shops and products
        @unlink($_SERVER['DOCUMENT_ROOT'] . Zend_Registry::get('config')->pictureupload->path . Zend_Registry::get('config')->productpictures->dir . '/' . $this->filename);
        @unlink($_SERVER['DOCUMENT_ROOT'] . Zend_Registry::get('config')->pictureupload->path . Zend_Registry::get('config')->productpictures->smalldir . '/' . $this->filename);
    }

    public function getAssociatedProducts(){
        $ret = array();
        $db = self::getDbTable()->getAdapter();
        $result = $db->query("SELECT product_id FROM epelia_products_pictures WHERE picture_id = ?", $this->id);
        if(is_null($result)){
            return array();
        }
        foreach($result as $r){
            $ret[] = Model_Product::find($r);
        }
        return $ret;
    }

    public function getAssociatedShops(){
        $ret = array();
        $db = self::getDbTable()->getAdapter();
        $result = $db->query("SELECT shop_id FROM epelia_shops_pictures WHERE picture_id = ?", $this->id);
        if(is_null($result)){
            return array();
        }
        foreach($result as $r){
            $ret[] = Model_Shop::find($r);
        }
        return $ret;
    }

    public static function deleteForProduct($productID){
        $db = self::getDbTable()->getAdapter();
        $query = $db->query('DELETE FROM epelia_products_pictures WHERE product_id = ?', $productID);
    }

    public static function deleteForShop($shopID){
        $db = self::getDbTable()->getAdapter();
        $query = $db->query('DELETE FROM epelia_products_shops WHERE shop_id = ?', $shopID);
    }
}
