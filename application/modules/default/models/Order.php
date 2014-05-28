<?php

class Model_Order extends Model_ModelAbstract
{
    public $id;
    public $user_id;
    public $shop_id;
    public $delivery_addr_id = null;
    public $billing_addr_id = null;
    public $status = 'in_process';
    public $created;
    public $shipping = 0.00;
    public $send_date = null;
    public $order_number;

    private $_items = null;
    private $_shop = null;
    private $_user = null;

    private $_delivery_address = null;
    private $_billing_address = null;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Order();
    }

    public function getShop(){
        if(is_null($this->_shop) && $this->shop_id){
            $this->_shop = Model_Shop::find($this->shop_id);
        }
        return $this->_shop;
    }

    public function getUser(){
        if(is_null($this->_user) && $this->user_id){
            $this->_user = Model_User::find($this->user_id);
        }
        return $this->_user;
    }

    public function getDeliveryAddress(){
        if(is_null($this->_delivery_address) && $this->delivery_addr_id){
            $this->_delivery_address = Model_Address::find($this->delivery_addr_id);
        }
        return $this->_delivery_address;
    }

    public function getBillingAddress(){
        if(is_null($this->_billing_address) && $this->billing_addr_id){
            $this->_billing_address = Model_Address::find($this->billing_addr_id);
        }
        return $this->_billing_address;
    }


    /**
     * structure: 
     *      product_id
     *      product_name
     *      value
     *      quantity
     *      unit_type
     *      content_type
     *      contents
     *      price_quantity
     *      added
     *      tax
     */
    public function getProducts(){
        if(is_null($this->_items)){
            $db = self::getDbTable()->getAdapter();
            $select = $db->select();       

            $select->where('order_id = ?', $this->id);
            $select->from('epelia_products_orders');

            $this->_items = $db->fetchAll($select);
        }
        return $this->_items;
    }

    public static function findByUser($userID, $status = null){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('user_id = ?', $userID);
        if(!is_null($status)){
            $select->where('status = ?', $status);
        }
        $select->order('id DESC');
        
        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findByShop($shopID, $status = null){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('shop_id = ?', $shopID);
        if(!is_null($status)){
            $select->where('status = ?', $status);
        }
        $select->order('id DESC');
        
        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findSendByShop($shopID, $month, $year){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('shop_id = ?', $shopID);
        $select->where('status = ?', 'complete');
        $select->where('EXTRACT(MONTH FROM send_date) = ?', $month);
        $select->where('EXTRACT(YEAR FROM send_date) = ?', $year);
        
        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;

    }

    public static function findByStatus($status){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('status = ?', $status);
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

    public static function getMaxYearSend(){
        $query = "SELECT MAX(EXTRACT(YEAR FROM send_date)) AS maxyear FROM epelia_orders WHERE status = 'complete'";
        $res = self::getDbTable()->getAdapter()->fetchCol($query);
        return $res[0];
    }
 
    public static function getMinYearSend(){
        $query = "SELECT MIN(EXTRACT(YEAR FROM send_date)) AS minyear FROM epelia_orders WHERE status = 'complete'";
        $res = self::getDbTable()->getAdapter()->fetchCol($query);
        return $res[0];
    }


    public function delete(){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $this->id);
        $table->delete($where); // by ON CASCADE datasets in epelia_products_orders are removed 
    }

    public function addProduct($productID, $productName, $value, $quantity, $unit_type, $content_type, $contents, $price_quantity, $tax){
        $this->_items[] = array(
            'product_id' => $productID,
            'product_name' => $productName,
            'value' => $value,
            'quantity' => $quantity, 
            'unit_type' => $unit_type,
            'content_type' => $content_type,
            'contents' => $contents,
            'price_quantity' => $price_quantity,
            'tax' => $tax
        );
    }

    public function insertProducts(){
        if(!$this->id){ // this should not happen, but in case we dont have an id we need one
            $this->save();
        }
        $query = self::getDbTable()->getAdapter()->query('DELETE FROM epelia_products_orders WHERE order_id = ?', array($this->id));
        foreach($this->getProducts() as $item){
            $query = self::getDbTable()->getAdapter()->query('INSERT INTO epelia_products_orders(product_id, product_name, order_id, value, quantity, unit_type, content_type, contents, price_quantity, tax) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', array($item['product_id'], $item['product_name'], $this->id, $item['value'], $item['quantity'], $item['unit_type'], $item['content_type'], $item['contents'], $item['price_quantity'], $item['tax']));
        }
    }

    public function getPriceTotal(){
        $total = 0;
        foreach($this->getProducts() as $pr){
            $total += $pr['value'] * $pr['quantity'];
        }
        return $total + $this->shipping;
    }

    public function getValueforTaxes(){
        $taxValues = array(0 => 0, 7 => 0, 19 => 0); // 0, 7, 19
        foreach($this->getProducts() as $item){
            switch($item['tax']){
                case 0:
                    $taxValues[0] += ($item['value'] * $item['quantity']);
                    break;
                case 7:
                    $taxValues[7] += ($item['value'] * $item['quantity']);
                    break;
                case 19:
                    $taxValues[19] += ($item['value'] * $item['quantity']);
                    break;
            }

        }        
        return $taxValues;
    }

    public function getTaxes(){
        $taxValues = array(0 => 0, 7 => 0, 19 => 0); // 0, 7, 19
        foreach($this->getProducts() as $item){
            switch($item['tax']){
                case 7:
                    $taxValues[7] += ($item['value'] * $item['quantity'] * 0.07);
                    break;
                case 19:
                    $taxValues[19] += ($item['value'] * $item['quantity'] * 0.19);
                    break;
            }

        }
        switch($this->getShippingTax()){
            case 7:
                $taxValues[7] += ($this->shipping * 0.07);
                break;
            case 19:
                $taxValues[19] += ($this->shipping * 0.19);
                break;
        }
        return $taxValues;
    }

    public function getShippingTax(){
        $taxValues = $this->getValueForTaxes();
        if($taxValues[0] >= $taxValues[7] && $taxValues[0] >= $taxValues[19]){
            return 0;
        }
        if($taxValues[7] >= $taxValues[0] && $taxValues[7] >= $taxValues[19]){
            return 7;
        }
        return 19;
    }

}
