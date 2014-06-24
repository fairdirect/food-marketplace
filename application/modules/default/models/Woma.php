<?php

class Model_Woma extends Model_ModelAbstract
{
    public $id;
    public $name;
    public $url;
    public $logo_id;
    public $taxnumber;
    public $salestax_id;

    public $company;
    public $street;
    public $house;
    public $zip;
    public $city;
    public $country;
    public $phone;
    public $fax;
    public $small_business = false;
    public $register_id;
    public $register_court;
    public $office;
    public $representative;
    public $eco_control_board;
    public $eco_control_id;
    public $type = null;
    
    public $bank_account_holder;
    public $bank_account_number;
    public $bank_id;
    public $bank_name;
    public $bank_swift;
    public $bank_iban;

    public $description;
    public $history;
    public $history_picture_id;
    public $philosophy;
    public $procedure;
    public $procedure_picture_id;
    public $additional;

    public $created;

    private $_products = null;
    private $_categories = null;

    private $_logo = null;
    private $_history_picture = null;
    private $_procedure_picture = null;

    private $_shippingCosts = null;

    private $_country;

    private $_shop_ids = array();

    private $_shops = null;

    public function __construct($data = array())
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Woma();
    }

    public function delete(){
        $this->deleted = true;
        $this->save();
    }

    public static function getAll($limit = null, $offset = null, $search = ''){
        $db = self::getDbTable()->getAdapter();
        $ret = array();
        
        $query = 'SELECT * FROM epelia_womas';
        
        if($search){
            $search = '%' . $search . '%';
            $query .= ' WHERE id = ' . $db->quote(str_replace('%', '', intVal($search))) . ' OR representative ILIKE ' . $db->quote($search) . ' OR company ILIKE ' . $db->quote($search) . ' OR street ILIKE ' . $db->quote($search) . ' OR zip ILIKE ' .$db->quote($search) . ' OR city ILIKE ' . $db->quote($search) . ' OR taxnumber ILIKE ' . $db->quote($search) . ' OR salestax_id ILIKE ' . $db->quote($search);
        }
        if($limit && !$offset){
            $query .= ' LIMIT ' . $db->quote($limit);
        }
        if($limit && $offset){
            $query .= ' LIMIT ' . $db->quote($limit) . ',' . $db->quote($offset);
        }

        $result = $db->fetchAll($query);
        if($result){
            foreach($result as $r){
                $ret[] =  new self($r);
            }
        }
        return $ret;
    }

    public static function findByPlzAndDistance($plz, $country, $distance){
        $ret = array();
        $db = self::getDbTable()->getAdapter();

        $result = $db->fetchAll('SELECT * FROM epelia_womas WHERE zip IN (SELECT dest.plz FROM geodb_locations dest CROSS JOIN geodb_locations src WHERE ACOS(SIN(RADIANS(src.lat)) * SIN(RADIANS(dest.lat)) + COS(RADIANS(src.lat)) * COS(RADIANS(dest.lat)) * COS(RADIANS(src.lon) - RADIANS(dest.lon))) * 6380 < ? AND src.plz = ? AND src.country = ?)', array($distance, $plz, $country));
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findByUrl($url){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('url = ?', $url);
        
        $result = $table->fetchRow($select);
        if (is_null($result)) {
            return false;
        }
        return new self($result);
    }


    public static function getCount(){
        $select = self::getDbTable()->select();
        $select->from(self::getDbTable(), array('count(*) as amount'));
        $rows = self::getDbTable()->fetchAll($select);
        return($rows[0]->amount);       
    }

    public function insertShops(){
        if(!$this->id){ // this should not happen, but in case we dont have an id we need one
            $this->save();
        }
        $query = self::getDbTable()->getAdapter()->query('DELETE FROM epelia_womas_shops WHERE woma_id = ?', array($this->id));
        foreach($this->_shop_ids as $shopID){
            $query = self::getDbTable()->getAdapter()->query('INSERT INTO epelia_womas_shops(woma_id, shop_id) VALUES(?,?)', array($this->id, $shopID));
        }
    }

    public function getShopIds(){
        if(!$this->_shop_ids){
            $result = self::getDbTable()->getAdapter()->fetchAll('SELECT shop_id FROM epelia_womas_shops WHERE woma_id = ?', $this->id);

            if (is_null($result)) {
                return array();
            }
            foreach($result as $r){
                $this->_shop_ids[] = $r['shop_id'];
            }
        }
        return $this->_shop_ids;
    }

    public function getShops(){
        if(is_null($this->_shops)){
            $this->_shops = array();
            foreach($this->getShopIds() as $shopID){
                $this->_shops[] = Model_Shop::find($shopID);
            }
        }
        return $this->_shops;
    }

    public function getProducts($limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        if(is_null($this->_products)){
            $this->_products = Model_Product::findByWoma($this->id, $limit, $offset, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all);
        }
        return $this->_products;
    }

    public function getProductsByCategory($catID, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){ 
        $products = array();
        foreach($this->getShops() as $shop){
            $shopProducts = Model_Product::findByShopAndCategory($shop->id, $catID, $limit, $offset, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all);
            foreach($shopProducts as $shopPr){
                $products[] = $shopPr;
            }
        }
        return $products;
    }

    public function getProductsByAttribute($attributeID, $limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){ 
        $products = array();
        foreach($this->getShops() as $shop){
            $shopProducts = Model_Product::findByShopAndAttribute($shop->id, $attributeID, $limit, $offset, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all);
            foreach($shopProducts as $shopPr){
                $products[] = $shopPr;
            }
        }
        return $products;
    }    

    public function getCategories($onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        $categories = $categoryIds = array();
        foreach($this->getShops() as $shop){
            $shopCats = Model_ProductCategory::findByShop($shop->id, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all);
            foreach($shopCats as $cat){
                if(!in_array($cat->id, $categoryIds)){
                    $categoryIds[] = $cat->id;
                    $categories[] = $cat;
                }
            }
        }
        return $categories;
    }

    public function getAttributes($onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false, $type = 'allergen'){
        $attributes = $attributeIds = array();
        foreach($this->getShops() as $shop){
            $shopAttributes = Model_ProductAttribute::findByShop($shop->id, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all, $type);
            foreach($shopAttributes as $attr){
                if(!in_array($attr->id, $attributeIds)){
                    $attributeIds[] = $attr->id;
                    $attributes[] = $attr;
                }
            }
         }
         return $attributes;
    }

    public function getAttributeProducts(){
        $products = $productIds = array();
        $attributes = $this->getAttributes();
        foreach($attributes as $attr){
            $attrProducts = $this->getProductsByAttribute($attr->id);
            foreach($attrProducts as $attrProduct){
                if(!in_array($attrProduct->id, $productIds)){
                    $productIds[] = $attrProduct->id;
                    $products[] = $attrProduct;
                }
            }
        }
        return $products;
    }

    public function clearProducts(){
        $this->_products = null;
    }



    public function getLogo(){
        if(is_null($this->_logo) && !is_null($this->logo_id)){
            $this->_logo = Model_Picture::find($this->logo_id);
        }
        return $this->_logo;
    }

    public function getHistoryPicture(){
        if(is_null($this->_history_picture) && !is_null($this->history_picture_id)){
            $this->_history_picture = Model_Picture::find($this->history_picture_id);
        }
        return $this->_history_picture;
    }

    public function getProcedurePicture(){
        if(is_null($this->_procedure_picture) && !is_null($this->procedure_picture_id)){
            $this->_procedure_picture = Model_Picture::find($this->procedure_picture_id);
        }
        return $this->_procedure_picture;
    }

    public function getCountry(){
        if(is_null($this->_country)){
            $this->_country = Model_Country::find($this->country);
        }
        return $this->_country;
    }

    public function getShippingCosts(){
        if(is_null($this->_shippingCosts)){
            $this->_shippingCosts = Model_WomaShippingCost::findByWoma($this->id);
        }
        return $this->_shippingCosts;
    }

    public function getLink(){
        return '/womas/' . $this->url . '/';
    }

    
}
