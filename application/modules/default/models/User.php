<?php

class Model_User extends Model_ModelAbstract
{
    public $id;
    public $phpbb_id = 0;
    public $email;
    public $password = '';
    public $salt = '';
    public $birthday;
    public $type = 'customer';
    public $is_admin = false;
    public $is_wholesale = false;
    public $main_delivery_address_id;
    public $main_billing_address_id;
    public $last_delivery_address_id;
    public $last_billing_address_id;
    public $status = 'new';
    public $registered;
    public $last_login;
    public $deleted = false;
    public $newsletter = false;
    public $password_reset_token = '';

    public $woma_id = null;

    private $_addresses = null;

    private $_shop = null;

    private $_woma = null;

    private $_main_delivery_address = null;
    private $_main_billing_address = null;
    private $_last_delivery_address = null;
    private $_last_billing_address = null;

    private $_bank_accounts = null;

    private $_product_ratings = null;

    public static function refreshAuth(){
        if(Zend_Auth::getInstance()->hasIdentity()){
            $user_id = Zend_Auth::getInstance()->getIdentity()->id;
            if($user_id){
                Zend_Auth::getInstance()->getStorage()->write(self::find($user_id));
            }
        }
    }

    public function getMainDeliveryAddress(){
        if(is_null($this->_main_delivery_address) && $this->main_delivery_address_id){
            $this->_main_delivery_address = Model_Address::find($this->main_delivery_address_id);
        }
        return $this->_main_delivery_address;
    }

    public function getMainBillingAddress(){
        if(is_null($this->_main_billing_address) && $this->main_billing_address_id){
            $this->_main_billing_address = Model_Address::find($this->main_billing_address_id);
        }
        return $this->_main_billing_address;
    }

    public function getLastDeliveryAddress(){
        if(is_null($this->_last_delivery_address)){
            $this->_last_delivery_address = Model_Address::find($this->last_delivery_address_id);
        }
        return $this->_last_delivery_address;
    }

    public function getLastBillingAddress(){
        if(is_null($this->_last_billing_address)){
            $this->_last_billing_address = Model_Address::find($this->last_billing_address_id);
        }
        return $this->_last_billing_address;
    }

    public function getBankAccounts(){
        if(is_null($this->_bank_accounts)){
            $this->_bank_accounts = Model_BankAccount::findByUser($this->id);
        }
        return $this->_bank_accounts;
    }

    public function getAddresses(){
        if(is_null($this->_addresses)){
            $this->_addresses = Model_Address::findByUser($this->id);
        }
        return $this->_addresses;
    }

    public function getShop(){
        if($this->type != 'shop'){
            return false;
        }
        if(is_null($this->_shop)){
            $this->_shop = Model_Shop::findByUser($this->id);
        }
        return $this->_shop;
    }

    public function getWoma(){
        if($this->type != 'agent'){
            return false;
        }
        if(is_null($this->_woma)){
            $this->_woma = Model_Woma::find($this->woma_id);
        }
        return $this->_woma;
    }

    public function getShoppingCarts($status){
        return Model_ShoppingCart::findByUser($this->id, $status);
    }

    public function getOrders($status = null){
        return Model_Order::findByUser($this->id, $status);
    }

    public function getProductRatings(){
        if(is_null($this->_product_ratings)){
            $this->_product_ratings = Model_ProductRating::findByUser($this->id);
        }
        return $this->_product_ratings;
    }

    public function __construct($data){
        parent::init($data);
    }

    public static function getDbTable()
    {
        return new Model_DbTable_User();
    }


    public static function findByEmail($email, $getAll = false)
    {
        $table = self::getDbTable();
        $select = $table->select();

        $select->where('email = ?', $email);
        if(!$getAll){
            $select->where("deleted = '0'");
        }        
        $result = $table->fetchRow($select);
        if (is_null($result)) {
            return null;
        }
        return new self($result);
    }

    public function delete(){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $this->id);
        $table->delete($where);
    }

    public static function getAll($limit = null, $offset = null, $search = ''){
        $db = self::getDbTable()->getAdapter();
        $ret = array();
        
        $query = 'SELECT u.* FROM epelia_users u LEFT JOIN epelia_addresses a ON a.id = u.main_billing_address_id';
        
        if($search){
            $search = '%' . $search . '%';
            $query .= ' WHERE u.email ILIKE ' . $db->quote($search) . ' OR u.id = ' . $db->quote(str_replace('%', '', intVal($search))) . ' OR a.firstname ILIKE ' . $db->quote($search) . ' OR a.name ILIKE ' .$db->quote($search) . ' OR a.street ILIKE ' . $db->quote($search) . ' OR a.zip ILIKE ' .$db->quote($search) . ' OR a.city ILIKE ' . $db->quote($search);
        }
        if($limit && !$offset){
            $query .= ' LIMIT ' . $db->quote($limit);
        }
        if($limit && $offset){
            $query .= ' LIMIT ' . $db->quote($limit) . ' OFFSET ' . $db->quote($offset);
        }

        $result = $db->fetchAll($query);
        if($result){
            foreach($result as $r){
                $ret[] =  new self($r);
            }
        }
        return $ret;
    }

    public static function getCount($search = ''){
        $db = self::getDbTable()->getAdapter();
        
        $query = 'SELECT COUNT(u.id) AS amount FROM epelia_users u LEFT JOIN epelia_addresses a ON a.id = u.main_billing_address_id';
        
        if($search){
            $search = '%' . $search . '%';
            $query .= ' WHERE u.email ILIKE ' . $db->quote($search) . ' OR u.id = ' . $db->quote(str_replace('%', '', intVal($search))) . ' OR a.firstname ILIKE ' . $db->quote($search) . ' OR a.name ILIKE ' .$db->quote($search) . ' OR a.street ILIKE ' . $db->quote($search) . ' OR a.zip ILIKE ' .$db->quote($search) . ' OR a.city ILIKE ' . $db->quote($search);
        }
        $result = $db->fetchAll($query);
        return $result[0]['amount'];
    }


    public static function getCustomers($limit = null, $offset = null){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('type = ?', 'customer');
        $select->order('id DESC');
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
}
