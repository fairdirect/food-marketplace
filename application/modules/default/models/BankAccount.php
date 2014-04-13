<?php

class Model_BankAccount extends Model_ModelAbstract
{
    public $id;
    public $user_id;
    public $account_nr;
    public $account_holder;
    public $bank_id;
    public $bank_name;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_BankAccount();
    }

    public static function findByUser($userID){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('user_id = ?', $userID);
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


}
