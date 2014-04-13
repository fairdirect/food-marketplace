<?php

class Model_ProductRating extends Model_ModelAbstract
{
    public $id;
    public $product_id;
    public $user_id;
    public $comment;
    public $rating;
    public $status = 'accepted';
    public $created;

    private $_product = null;
    private $_user = null;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_ProductRating();
    }

    public static function getAll(){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

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


    public static function findByProduct($productID, $onlyAccepted = false){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('product_id = ?', $productID);

        if($onlyAccepted){
            $select->where("status = 'accepted'");
        }

        $select->order('created DESC');
        
        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function findByUser($userID, $onlyAccepted = false){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('user_id = ?', $userID);

        if($onlyAccepted){
            $select->where("status = 'accepted'");
        }

        $select->order('created DESC');
        
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

    public function getProduct(){
        if(is_null($this->_product)){
            $this->_user = Model_Product::find($this->product_id);
        }
        return $this->_product;
    }

    public function delete(){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $this->id);
        $table->delete($where); 
    }

}
