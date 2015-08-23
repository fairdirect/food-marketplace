<?php

class Model_Homegroup extends Model_ModelAbstract
{
    public $id;
    public $name;

    private $_products;

    public function __construct($data = array())
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Homegroup();
    }

    public function delete(){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $this->id);
        $table->delete($where);
    }

    public static function findAll(){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->order('id ASC');

        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public function getProducts($limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        if(is_null($this->_products)){
            $this->_products = Model_Product::findByHomegroup($this->id, $limit, $offset, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all);
        }
        return $this->_products;
    }

    public function addProduct($productId){
        $db = self::getDbTable()->getAdapter();
        try{
            $db->query("INSERT INTO epelia_products_home_groups_products(group_id, product_id) VALUES(?, ?)", array($this->id, $productId));
            return true;
        } catch(Exception $e){
            return false;
        }
    }

    public function deleteProduct($productId){
        $db = self::getDbTable()->getAdapter();
        try{
            $db->query("DELETE FROM epelia_products_home_groups_products WHERE group_id = ? AND product_id = ?", array($this->id, $productId));
            return true;
        } catch(Exception $e){
            return false;
        }
    }

}
