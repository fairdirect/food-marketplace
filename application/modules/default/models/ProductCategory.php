<?php

class Model_ProductCategory extends Model_ModelAbstract
{
    public $id;
    public $name;
    public $product_group_id;

    private $_count; // this is a flexible value, assigned as needed

    private $_productGroup = null;
    private $_products = null;

    public function __construct($data)
    {
        parent::init($data);
    }

    public function delete(){
        $this->deleted = true;
        $this->save();
    }

    public function setCount($count){
        $this->_count = $count;
        return $this;
    }

    public function getCount(){
        return $this->_count;
    }

    public static function getDbTable(){
        return new Model_DbTable_ProductCategory();
    }

    public function getProductGroup(){
        if(is_null($this->_productGroup)){
            $this->_productGroup = Model_ProductGroup::find($this->product_group_id);
        }
        return $this->_productGroup;
    }

    public function clearProducts(){
        $this->_products = null;
    }

    public function getProducts($limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        if(is_null($this->_products)){
            $this->_products = Model_Product::findByCategory($this->id, $limit, $offset, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all);
        }
        return $this->_products;
    }

    public function getProductCount($onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        return count($this->getProducts(null, null, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all));
    }

    public static function findByGroup($groupID, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join
        $ret = array();

        if($onlyBio || $onlyDiscount || $onlyWholesale || $onlyActivated){;
            $select->join('epelia_products', $table->getTableName() . '.id = epelia_products.category_id', array());
        }

        $select->where('product_group_id = ?', $groupID);

        if($onlyBio){
            $select->where('epelia_products.is_bio = ?', true);
        }
        if($onlyDiscount){
            $select->where('epelia_products.is_discount = ?', true);
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', 'epelia_products.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale = ?', true);

        }

        if($onlyActivated){
            $select->where('epelia_products.active = ?', true);
        }

        $select->group($table->getTableName() . '.id');
        $select->order('name ASC');
        
        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findByShop($shopID, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from(array($table->getTableName(), 'epelia_products'), array('*', 'COUNT(epelia_products.id)')); // need to use adapter select here to be able to join
        $ret = array();

        $select->join('epelia_products', $table->getTableName() . '.id = epelia_products.category_id', array());

        $select->where('epelia_products.shop_id = ?', $shopID);

        if($onlyBio){
            $select->where('epelia_products.is_bio = ?', true);
        }
        if($onlyDiscount){
            $select->where('epelia_products.is_discount = ?', true);
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', 'epelia_products.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale = ?', true);

        }

        if($onlyActivated){
            $select->where('epelia_products.active = ?', true);
        }

        if(!$all){
            $select->where('epelia_products.deleted != ?', true);
        }

        $select->group($table->getTableName() . '.id');
        $select->order($table->getTableName() . '.name DESC');

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $cat = new self($r);
            $cat->setCount($r['count']);
            $ret[] = $cat; 
        }
        return $ret;
    }


    public static function findAll(){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->order('product_group_id DESC');
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
}
