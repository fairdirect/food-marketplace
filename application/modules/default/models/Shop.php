<?php

class Model_Shop extends Model_ModelAbstract
{
    public $id;
    public $user_id;
    public $name;
    public $url;
    public $provision = 4;
    public $logo_id;
    public $taxnumber;
    public $salestax_id;
    public $status = 'new';

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

    private $shop_type;

    private $_woma_ids = array(); // must be private for save()

    private $_womas = null;

    private $_user = null;
    private $_products = null;
    private $_categories = null;

    private $_logo = null;
    private $_history_picture = null;
    private $_procedure_picture = null;

    private $_shippingCosts = null;

    private $_country;

    public function __construct($data = array())
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Shop();
    }

    public function delete(){
        $this->deleted = true;
        $this->save();
    }

    public static function getAll($limit = null, $offset = null, $search = ''){
        $db = self::getDbTable()->getAdapter();
        $ret = array();
        
        $query = 'SELECT s.* FROM epelia_shops s JOIN epelia_users u ON s.user_id = u.id';
        
        if($search){
            $search = '%' . $search . '%';
            $query .= ' WHERE u.email ILIKE ' . $db->quote($search) . ' OR s.id = ' . $db->quote(str_replace('%', '', intVal($search))) . ' OR s.name ILIKE ' . $db->quote($search) . ' OR s.company ILIKE ' . $db->quote($search) . ' OR s.street ILIKE ' . $db->quote($search) . ' OR s.zip ILIKE ' .$db->quote($search) . ' OR s.city ILIKE ' . $db->quote($search) . ' OR s.taxnumber ILIKE ' . $db->quote($search) . ' OR s.salestax_id ILIKE ' . $db->quote($search);
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

        $result = $db->fetchAll('SELECT * FROM epelia_shops WHERE zip IN (SELECT dest.plz FROM geodb_locations dest CROSS JOIN geodb_locations src WHERE ACOS(SIN(RADIANS(src.lat)) * SIN(RADIANS(dest.lat)) + COS(RADIANS(src.lat)) * COS(RADIANS(dest.lat)) * COS(RADIANS(src.lon) - RADIANS(dest.lon))) * 6380 < ? AND src.plz = ? AND src.country = ?)', array($distance, $plz, $country));
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function getCount(){
        $select = self::getDbTable()->select();
        $select->from(self::getDbTable(), array('count(*) as amount'));
        $rows = self::getDbTable()->fetchAll($select);
        return($rows[0]->amount);       
    }

    public static function getActivated($limit = null, $offset = null){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('status = ?', 'activated');
        $select->order('name DESC');
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

    public function getUser(){
        if(is_null($this->_user)){
            $this->_user = Model_User::find($this->user_id);
        }
        return $this->_user;
    }

    public function getProducts($limit = null, $offset = null, $onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        if(is_null($this->_products)){
            $this->_products = Model_Product::findByShop($this->id, $limit, $offset, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all);
        }
        return $this->_products;
    }

    public function getCategories($onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $all = false){
        if(is_null($this->_categories)){
            $this->_categories = Model_ProductCategory::findByShop($this->id, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $all);
        }
        return $this->_categories;
    }

    public function getShippingCosts(){
        if(is_null($this->_shippingCosts)){
            $this->_shippingCosts = Model_ShippingCost::findByShop($this->id);
        }
        return $this->_shippingCosts;
    }

    public static function findByUser($userID){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('user_id = ?', $userID);
        
        $result = $table->fetchRow($select);
        if (is_null($result)) {
            return false;
        }
        return new self($result);
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

    public function getSalesData($month, $year){
        $orders = $this->getOrdersByDate($month, $year);
        $total = $shipping = 0;
        foreach($orders as $order){
            $shipping += $order->shipping;
            $orderProducts = $order->getProducts();
            foreach($orderProducts as $item){
                $total += $item['value'] * $item['quantity'];
            }
        }
        $provisionNetto = round($total * $this->provision / 100, 2);
        $provisionMwSt = round($provisionNetto * 0.19, 2);
        $provisionBrutto = round($provisionNetto + $provisionMwSt, 2);
        $payout = round($total + $shipping - $provisionBrutto, 2);

        return array(
            'total' => $total,
            'shipping' => $shipping,
            'provision_netto' => $provisionNetto,
            'provision_mwst' => $provisionMwSt,
            'provision_brutto' => $provisionBrutto,
            'payout' => $payout
        );
    }

    public static function getShopsWithOrders($month, $year){ // using send_date, if no order send yet no shop will not be returned
        $db = self::getDbTable()->getAdapter();
        $ret = array();

        $result = $db->fetchAll('SELECT s.* FROM epelia_shops s JOIN epelia_orders o ON s.id = o.shop_id WHERE EXTRACT(MONTH FROM o.send_date) = ? AND EXTRACT(YEAR FROM o.send_date) = ? GROUP BY s.id', array($month, $year));

        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }
 
    public function getOrdersByDate($month, $year){
        return Model_Order::findSendByShop($this->id, $month, $year);
    }

    public static function getRandomShopWithProducts($minProductCount = null){
        $db = self::getDbTable()->getAdapter();
        
        $query = 'SELECT s.* FROM epelia_shops s LEFT JOIN epelia_products p ON s.id = p.shop_id';
        if(!is_null($minProductCount)){
            $query .= " WHERE p.active = true AND p.deleted = false";
        }
        $query .= ' GROUP BY s.id';
        if(!is_null($minProductCount)){
            $query .= ' HAVING COUNT(p.id) >= ' . $db->quote($minProductCount);
        }
        $query .= ' ORDER BY random() LIMIT 1';

        $result = $db->fetchAll($query);
        if($result){
            return new self($result[0]);
        }
    }

    public function getShopType(){
        switch($this->shop_type){
            case 'manufacturer':
                return 'Hersteller';
                break;
            case 'importer':
                return 'Importeur';
                break;
            default:
                return 'Händler';
                break;
        }
    }

    public function getShopTypeIcon(){
        switch($this->shop_type){
            case 'manufacturer':
                return 'role_icon_2';
                break;
            case 'importer':
                return 'role_icon_4';
                break;
            default:
                return 'role_icon_3';
                break;
        }
    }

    public function setWomaIds($womaIds){
        $this->_woma_ids = $womaIds;
    }

    public function getWomaIds(){
        if(!$this->_woma_ids){
            $result = self::getDbTable()->getAdapter()->fetchAll('SELECT woma_id FROM epelia_womas_shops WHERE shop_id = ?', $this->id);

            if (is_null($result)) {
                return array();
            }
            foreach($result as $r){
                $this->_woma_ids[] = $r['woma_id'];
            }
        }
        return $this->_woma_ids;
    }

    public function insertWomas(){
        if(!$this->id){ // this should not happen, but in case we dont have an id we need one
            $this->save();
        }
        $query = self::getDbTable()->getAdapter()->query('DELETE FROM epelia_womas_shops WHERE shop_id = ?', array($this->id));
        foreach($this->_woma_ids as $womaID){
            $query = self::getDbTable()->getAdapter()->query('INSERT INTO epelia_womas_shops(woma_id, shop_id) VALUES(?,?)', array($womaID, $this->id));
        }
    }

    public function getWomas(){
        if(is_null($this->_womas)){
            $this->_womas = array();
            foreach($this->_woma_ids as $womaID){
                $this->_womas[] = Model_Woma::find($womaID);
            }
        }
        return $this->_womas;
    }


}
