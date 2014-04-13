<?php

class Model_ShippingCost extends Model_ModelAbstract
{
    public $shop_id;
    public $country_id;
    public $value;
    public $free_from = null;

    private $_country;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_ShippingCost();
    }

    public function save(){
        $this->delete();
        $this->getDbTable()->insert($this->toArray());
    }


    public static function findByShop($shopID){
        $ret = array();
        $db = self::getDbTable()->getAdapter();
        $result = $db->query("SELECT * FROM epelia_shipping_costs WHERE shop_id = ?", $shopID);
        if(is_null($result)){
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public function getCountry(){
        if(is_null($this->_country)){
            $this->_country = Model_Country::find($this->country_id);
        }
        return $this->_country;
    }

    public function delete(){
        $table = self::getDbTable();
        $where = array();
        $where[] = $table->getAdapter()->quoteInto('shop_id = ?', $this->shop_id);
        $where[] = $table->getAdapter()->quoteInto('country_id = ?', $this->country_id);
        $table->delete($where);
    }

    public static function deleteForShop($shopID){
        $db = self::getDbTable()->getAdapter();
        $query = $db->query('DELETE FROM epelia_shipping_costs WHERE shop_id = ?', $shopID);
    }
}
