<?php

class Model_ProductGroup extends Model_ModelAbstract
{
    public $id;
    public $name;
    public $main_category;

    private $_categories = null;

    public function getCategories($onlyBio = false, $onlyDiscount = false, $onlyWholesale = false){
        if(is_null($this->_categories)){
            $this->_categories = Model_ProductCategory::findByGroup($this->id, $onlyBio, $onlyDiscount, $onlyWholesale);
        }
        return $this->_categories;
    }

    public function clearCategories(){
        $this->_categories = null;
    }

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getAll(){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->order('main_category DESC');
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


    public function getProducts($limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true){
        return Model_Product::findByGroup($this->id, $limit, $offset, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated);
    }

    public function getProductCount($onlyBio = false, $onlyDiscount = false, $onlyWholesale = false){
        $count = 0;
        foreach($this->getCategories() as $cat){
            $count += $cat->getProductCount($onlyBio, $onlyDiscount, $onlyWholesale);
        }
        return $count;
    }

    public function delete(){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $this->id);
        $table->delete($where);
    }

    public static function getDbTable(){
        return new Model_DbTable_ProductGroup();
    }

    public static function getByType($mainCategory, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $onlyFromRegion = false){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join
        $ret = array();
 
        if($mainCategory){
            $select->where('main_category = ?', $mainCategory);
        }
        else{
            $select->where('main_category IS NULL');
        }

        if($onlyBio || $onlyDiscount || $onlyWholesale || $onlyActivated || $onlyFromRegion){
            $select->join('epelia_product_categories', $table->getTableName() . '.id = epelia_product_categories.product_group_id', array());
            $select->join('epelia_products', 'epelia_product_categories.id = epelia_products.category_id', array());
            $select->join('epelia_shops', 'epelia_products.shop_id = epelia_shops.id', array());
            $select->join('epelia_regions', 'epelia_shops.zip = epelia_regions.plz AND epelia_shops.country = epelia_regions.country', array());

            if($onlyBio){
                $select->where('is_bio = ?', 'true');
            }
            if($onlyDiscount){
                $select->where('is_discount = ?', 'true');
            }
            if($onlyWholesale){
                $select->join('epelia_product_prices', 'epelia_products.id = epelia_product_prices.product_id', array());
                $select->where('epelia_product_prices.is_wholesale', 'true');
            }
            if($onlyActivated){
                $select->where('epelia_products.active = ?', true);
            }
            if($onlyActivated){
                $select->where('epelia_products.active = ?', true);
            }
            if($onlyFromRegion){
                $region = Model_Region::getCurrentRegion();
                if(!$region) {
                    return array();
                }
                $select->where('epelia_shops.country = ?', $region->country);
                $select->where('epelia_regions.name = ?', $region->name);
            }

            $select->where('epelia_products.deleted = ?', '0');
        }
        $select->group($table->getTableName() . '.id');
        $select->order($table->getTableName() . '.name ASC');

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }

        return $ret;
    }  
}
