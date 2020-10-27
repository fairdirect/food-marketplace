<?php

class Model_Product extends Model_ModelAbstract
{
    public $id;
    public $shop_id;
    public $name;
    public $description;
    public $num_views = 0;
    public $active = true;
    public $stock = null;
    public $created;
    public $deleted = false;
    public $category_id;
    public $is_bio;
    public $is_discount;
    public $tags;
    public $traces;
    public $ingredients;
    public $main_picture_id;
    public $producttype;
    public $best_before;

    private $_shop = null;
    private $_attributes = null;
    private $_allergenes = null;
    private $_category = null;
    private $_prices = null;
    private $_pictures = null;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Product();
    }

    public function setAttributes($attributes = array()){
        Model_ProductAttribute::deleteForProduct($this->id);
        foreach($attributes as $attr){
            $query = self::getDbTable()->getAdapter()->query('INSERT INTO epelia_products_product_attributes(product_id, product_attribute_id) VALUES(?, ?)', array($this->id, $attr));
        }
    }

    public static function findByHomegroup($homegroupID, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join
        $select->join('epelia_products_home_groups_products', $table->getTableName() . '.id = epelia_products_home_groups_products.product_id', array());

        $ret = array();
   
        $select->where('epelia_products_home_groups_products.group_id = ?', $homegroupID);

        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', $table->getTableName() . '.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale', 'true');
            $select->group($table->getTableName() . '.id');
        }

        if($onlyActivated){
            $select->where($table->getTableName() . '.active = ?', true);
        }

        if(!$all){
            $select->where($table->getTableName() . '.deleted != ?', true);
        }

        $select->order($table->getTableName() . '.name ASC');
        $select->limit($limit, $offset);

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }


    public function getAttributes(){
        if(is_null($this->_attributes)){
            $this->_attributes = Model_ProductAttribute::getByProduct($this->id);
        }
        return $this->_attributes;
    }

    public function getAllergenes(){
         if(is_null($this->_allergenes)){
            $this->_allergenes = Model_ProductAttribute::getAllergenesByProduct($this->id);
        }
        return $this->_allergenes;
    }       

    public function addPicture($pictureID){
        $query = self::getDbTable()->getAdapter()->query('INSERT INTO epelia_products_pictures(product_id, picture_id) VALUES(?, ?)', array($this->id, $pictureID));
    } 

    public function setPictures($pictures = array()){
        Model_Picture::deleteForProduct($this->id);
        foreach($pictures as $pic){
            $query = self::getDbTable()->getAdapter()->query('INSERT INTO epelia_products_pictures(product_id, picture_id) VALUES(?, ?)', array($this->id, $pic));
        }
    }

    public function getPictures(){
        if(is_null($this->_pictures)){
            $this->_pictures = Model_Picture::getByProduct($this->id);
        }
        return $this->_pictures;
    }

    public function getDefaultPicture(){
        $pics = $this->getPictures();
        if($pics){
            foreach($pics as $pic){
                if($pic->id == $this->main_picture_id){
                    return $pic;
                }
            } 
		// at this point we have no default pic, take the first
		return $pics[0];
        }
        return false;
    }

    public function getPrices(){
        if(is_null($this->_prices)){
            $this->_prices = Model_ProductPrice::findByProduct($this->id);
        }
        return $this->_prices;
    }

    public function getNormalPrices(){
        $ret = array();
        foreach($this->getPrices() as $price){
            if(!$price->is_wholesale){
                $ret[] = $price;
            }
        }
        return $ret;
    }

    public function getFirstNormalPrice(){
        $prices = $this->getNormalPrices();
        if($prices){
            return $prices[0];
        }
        else{
            return false;
        }
    }

    public function getWholesalePrices(){
        $ret = array();
        foreach($this->getPrices() as $price){
            if($price->is_wholesale){
                $ret[] = $price;
            }
        }
        return $ret;
    }

    public function getFirstWholesalePrice(){
        $prices = $this->getWholesalePrices();
        if($prices){
            return $prices[0];
        }
        return false;
    }

    public static function getCount($onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all, $search = ''){
        $table = self::getDbTable();
        $db = $table->getAdapter();
        $select = $db->select();
        $select->from(array('p' => $table->getTableName()), array('count(*) as amount'));

        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', 'p.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale', 'true');
            $select->group('p.id');
        }

        if($onlyActivated){
            $select->where('p.active = ?', true);
        }

        if(!$all){
            $select->where('p.deleted != ?', true);
        }

        if($search){
            $search = '%' . $search . '%';
            $select->join(array('s' => 'epelia_shops'), 'p.shop_id = s.id', array(''));
            $select->join(array('u' => 'epelia_users'), 's.user_id = u.id', array(''));
            $select->where('p.id = ' . $db->quote(intVal(str_replace('%', '', $search))) . ' OR p.name ILIKE ' . $db->quote($search) . ' OR s.name ILIKE ' . $db->quote($search) . ' OR u.email ILIKE ' . $db->quote($search));
        }

        $rows = self::getDbTable()->getAdapter()->fetchAll($select);
        return($rows[0]['amount']);       
    }


    public static function getAll($limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false, $search = ''){
        $table = self::getDbTable();
        $db = $table->getAdapter();
        $select = $db->select()->from(array('p' => $table->getTableName()), '*'); 
        $ret = array();
   
        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', 'p.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale', 'true');
            $select->group('p.id');
        }

        if($onlyActivated){
            $select->where('p.active = ?', true);
        }

        if(!$all){
            $select->where('p.deleted != ?', true);
        }

        if($search){
            $search = '%' . $search . '%';
            $select->join(array('s' => 'epelia_shops'), 'p.shop_id = s.id', array(''));
            $select->join(array('u' => 'epelia_users'), 's.user_id = u.id', array(''));
            $select->where('p.id = ' . $db->quote(intVal(str_replace('%', '', $search))) . ' OR p.name ILIKE ' . $db->quote($search) . ' OR s.name ILIKE ' . $db->quote($search) . ' OR u.email ILIKE ' . $db->quote($search));
        }

        $select->order('p.id DESC');
        $select->limit($limit, $offset);

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public function getShop(){
        if(is_null($this->_shop) && $this->shop_id){
            $this->_shop = Model_Shop::find($this->shop_id);
        }
        return $this->_shop;
    }

    public function getCategory(){
        if(is_null($this->_category) && $this->category_id){
            $this->_category = Model_ProductCategory::find($this->category_id);
        }
        return $this->_category;
    }

    public function delete(){
        $this->deleted = true;
        $this->save();
    }

    public function toFormArray(){
        $ret = parent::toFormArray();
        if(is_null($this->stock)){
            $ret['unlimited_stock'] = 1;
        }
        foreach($this->getAttributes() as $attr){
            $ret[$attr->type][] = $attr->id;
        }
        $normalCounter = $wholesaleCounter = 0;
        foreach($this->getPrices() as $price){
            if($price->is_wholesale){
                $type = 'wholesale';
                $counter = ++$wholesaleCounter;
            }
            else{
                $type = 'normal';
                $counter = ++$normalCounter;
            }
            $ret[$type . '_amount_' . $counter] = $price->quantity;
            $ret[$type . '_unit_' . $counter] = $price->unit_type_id;
            $ret[$type . '_content_' . $counter] = $price->contents;
            $ret[$type . '_content_type_' . $counter] = $price->content_type_id;
            $ret[$type . '_euro_' . $counter] = (int)$price->value;
            $ret[$type . '_cent_' . $counter] = ($price->value - (int)$price->value) * 100;
        }
        return $ret;
    }

    public static function findByCategory($categoryID, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join
        $ret = array();
   
        $select->where('category_id = ?', $categoryID);

        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', $table->getTableName() . '.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale', 'true');
            $select->group($table->getTableName() . '.id');
        }

        if($onlyActivated){
            $select->where($table->getTableName() . '.active = ?', true);
        }

        if(!$all){
            $select->where($table->getTableName() . '.deleted != ?', true);
        }

        $select->order($table->getTableName() . '.name ASC');
        $select->limit($limit, $offset);

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findByGroup($groupID, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join
        $select->join('epelia_product_categories', $table->getTableName() . '.category_id = epelia_product_categories.id', array());
        $select->join('epelia_product_groups', 'epelia_product_categories.product_group_id = epelia_product_groups.id', array());
        $ret = array();
   
        $select->where('epelia_product_groups.id = ?', $groupID);

        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', $table->getTableName() . '.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale', 'true');
            $select->group($table->getTableName() . '.id');
        }

        if($onlyActivated){
            $select->where($table->getTableName() . '.active = ?', true);
        }

        if(!$all){
            $select->where($table->getTableName() . '.deleted != ?', true);
        }

        $select->order($table->getTableName() . '.name ASC');
        $select->limit($limit, $offset);

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findByShop($shopID, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join
        $ret = array();
   
        $select->where('shop_id = ?', $shopID);

        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', $table->getTableName() . '.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale', 'true');
            $select->group($table->getTableName() . '.id');
        }

        if($onlyActivated){
            $select->where($table->getTableName() . '.active = ?', true);
        }

        if(!$all){
            $select->where($table->getTableName() . '.deleted != ?', true);
        }

        $select->order($table->getTableName() . '.name ASC');
        $select->limit($limit, $offset);

        $result = $select->query()->fetchAll();

        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }


    public static function findByShopAndCategory($shopID, $catID, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join
        $ret = array();
   
        $select->where('shop_id = ?', $shopID);
        $select->where('category_id = ?', $catID);

        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', $table->getTableName() . '.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale', 'true');
            $select->group($table->getTableName() . '.id');
        }

        if($onlyActivated){
            $select->where($table->getTableName() . '.active = ?', true);
        }

        if(!$all){
            $select->where($table->getTableName() . '.deleted != ?', true);
        }

        $select->order($table->getTableName() . '.name ASC');
        $select->limit($limit, $offset);

        $result = $select->query()->fetchAll();

        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findFeaturedHomeByShop($shopID){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*');
        $ret = array();
   
        $select->where('id IN (SELECT product_id FROM epelia_shops_featured_products_home WHERE shop_id = ?)', array($shopID));

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


    public static function findByWoma($womaID, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $table = self::getDbTable();
        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join
        $ret = array();
   
        $select->where('shop_id IN (SELECT id FROM epelia_shops WHERE woma_id = ?)', $womaID);

        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', $table->getTableName() . '.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale', 'true');
            $select->group($table->getTableName() . '.id');
        }

        if($onlyActivated){
            $select->where($table->getTableName() . '.active = ?', true);
        }

        if(!$all){
            $select->where($table->getTableName() . '.deleted != ?', true);
        }

        $select->order($table->getTableName() . '.name ASC');
        $select->limit($limit, $offset);

        $result = $select->query()->fetchAll();

        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }



    public static function findBySearchString($search, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('(name ILIKE ? OR description ILIKE ? OR ingredients ILIKE ? OR tags ILIKE ?)', '%' . $search . '%');
        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }

        if($onlyActivated){
            $select->where($table->getTableName() . '.active = ?', true);
        }

        if(!$all){
            $select->where($table->getTableName() . '.deleted != ?', true);
        }

        $select->order('name ASC');
        $select->limit($limit, $offset);
        
        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findByAttribute($attributeID, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $ret = array();
        $table = self::getDbTable();

        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join

        $select->join('epelia_products_product_attributes', $table->getTableName() . '.id = epelia_products_product_attributes.product_id', array());
        $select->where('epelia_products_product_attributes.product_attribute_id = ?', $attributeID);

        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', $table->getTableName() . '.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale', 'true');
            $select->group($table->getTableName() . '.id');
        }

        if($onlyActivated){
            $select->where($table->getTableName() . '.active = ?', true);
        }

        if(!$all){
            $select->where($table->getTableName() . '.deleted != ?', true);
        }

        $select->group($table->getTableName() . '.id'); 
        $select->limit($limit, $offset);

        $result = $select->query()->fetchAll();
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findByShopAndAttribute($shopID, $attributeID, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $ret = array();
        $table = self::getDbTable();

        $select = $table->getAdapter()->select()->from($table->getTableName(), '*'); // need to use adapter select here to be able to join
        $select->where('shop_id = ?', $shopID);

        $select->join('epelia_products_product_attributes', $table->getTableName() . '.id = epelia_products_product_attributes.product_id', array());
        $select->where('epelia_products_product_attributes.product_attribute_id = ?', $attributeID);

        if($onlyBio){
            $select->where('is_bio = ?', 'true');
        }
        if($onlyDiscount){
            $select->where('is_discount = ?', 'true');
        }
        if($onlyWholesale){
            $select->join('epelia_product_prices', $table->getTableName() . '.id = epelia_product_prices.product_id', array());
            $select->where('epelia_product_prices.is_wholesale', 'true');
            $select->group($table->getTableName() . '.id');
        }

        if($onlyActivated){
            $select->where($table->getTableName() . '.active = ?', true);
        }

        if(!$all){
            $select->where($table->getTableName() . '.deleted != ?', true);
        }

        $select->group($table->getTableName() . '.id'); 
        $select->limit($limit, $offset);

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
