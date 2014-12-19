<?php

class Model_ProductAttribute extends Model_ModelAbstract
{
    public $id;
    public $name;
    public $type;
    public $opposite;

    private $_products;
 
    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_ProductAttribute();
    }

    public function delete(){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $this->id);
        $table->delete($where); // by ON CASCADE attributes are removed from products
    }

    public static function getAll(){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->order('type DESC');
        $select->order('name DESC');

        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }


    public static function getByType($type){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('type = ?', $type);
        $select->order('name DESC');
        
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
            $this->_products = Model_Product::findByAttribute($this->id, $limit, $offset, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all);
        }
        return $this->_products;
    }

    public function clearProducts(){
        $this->_products = null;
    }


    public function getProductCount($onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        return count($this->getProducts(null, null, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all));
    }

    public static function getByProduct($productID){
        $ret = array();
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join

        $select->join('epelia_products_product_attributes', $table->getTableName() . '.id = epelia_products_product_attributes.product_attribute_id', array());
        $select->where('epelia_products_product_attributes.product_id = ?', $productID);
        $select->group($table->getTableName() . '.id');       

        $select->order($table->getTableName() . '.type DESC');

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function getAllergenesByProduct($productID){
        $notIncluded = self::getNotIncludedAllergenesByProduct($productID);
        $nIds = array();
        foreach($notIncluded as $n){
            $nIds[] = $n->id;
        }

        $ret = array();
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); 
        $select->where($table->getTableName() . '.type = ?', 'allergen');
        $select->where($table->getTableName() . '.opposite IS NOT NULL');
        $select->where($table->getTableName() . '.opposite <> ?', '');
        if($nIds){
            $select->where($table->getTableName() . '.id NOT IN (?)', $nIds);
        }            

        $select->order($table->getTableName() . '.type DESC');

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function getNotIncludedAllergenesByProduct($productID){
        $ret = array();
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join

        $select->join('epelia_products_product_attributes', $table->getTableName() . '.id = epelia_products_product_attributes.product_attribute_id', array());
        $select->where('epelia_products_product_attributes.product_id = ?', $productID);
        $select->where($table->getTableName() . '.type = ?', 'allergen');
        $select->group($table->getTableName() . '.id');

        $select->order($table->getTableName() . '.type DESC');

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function deleteForProduct($productID){
        $db = self::getDbTable()->getAdapter();
        $query = $db->query('DELETE FROM epelia_products_product_attributes WHERE product_id = ?', $productID)->execute();
    }

    public static function findByShop($shopID, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false, $type = null){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from(array($table->getTableName(), 'epelia_products'), array('*')); // need to use adapter select here to be able to join
        $select->join('epelia_products_product_attributes', $table->getTableName() . '.id = epelia_products_product_attributes.product_attribute_id', array());

        $ret = array();

        $subSelect = $table->getAdapter()->select()->from('epelia_products', 'id'); 
        $subSelect->where('epelia_products.shop_id = ?', $shopID);

        if($onlyBio){
            $subSelect->where('epelia_products.is_bio = ?', true);
        }
        if($onlyDiscount){
            $subSelect->where('epelia_products.is_discount = ?', true);
        }
        if($onlyWholesale){
            $subSelect->join('epelia_product_prices', 'epelia_products.id = epelia_product_prices.product_id', array());
            $subSelect->where('epelia_product_prices.is_wholesale = ?', true);

        }

        if($onlyActivated){
            $subSelect->where('epelia_products.active = ?', true);
        }

        if(!$all){
            $subSelect->where('epelia_products.deleted != ?', true);
        }
        $select->where('epelia_products_product_attributes.product_id IN ?', $subSelect);

        if($type){
            $select->where('epelia_product_attributes.type = ?', $type);
        }

        $select->group($table->getTableName() . '.id');
        $select->order($table->getTableName() . '.name DESC');

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $cat = new self($r);

            $ret[] = $cat; 
        }
        return $ret;
    }
}
