<?php

class Model_ProductGroup extends Model_ModelAbstract
{
    public $id;
    public $name;
    public $type;

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

    public static function getByType($type, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join
        $ret = array();
 
        $select->where('type = ?', $type);

        if($onlyBio || $onlyDiscount || $onlyWholesale || $onlyActivated){
            $select->join('epelia_product_categories', $table->getTableName() . '.id = epelia_product_categories.product_group_id', array());
            $select->join('epelia_products', 'epelia_product_categories.id = epelia_products.category_id', array());

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
