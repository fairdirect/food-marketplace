<?php

class Model_Invoice extends Model_ModelAbstract
{
    public $id;
    public $shop_id;
    public $invoice_amount;
    public $file;
    public $month;
    public $year;
    public $payout;
    public $last_sent;
    public $last_sent_email;

    private $_shop = null;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Invoice();
    }
   
    public static function getByDate($month, $year){
        $ret = array();
        $db = self::getDbTable()->getAdapter();
        $result = $db->query("SELECT * FROM epelia_invoices WHERE month = ? AND year = ?", array($month, $year));
        if(is_null($result)){
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

    public static function findByShop($shopID){
        $ret = array();
        $db = self::getDbTable()->getAdapter();
        $result = $db->query("SELECT * FROM epelia_invoices WHERE shop_id = ?", $shopID);
        if(is_null($result)){
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

}
