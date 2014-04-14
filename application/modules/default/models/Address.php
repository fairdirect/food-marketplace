<?php

class Model_Address extends Model_ModelAbstract
{
    public $id;
    public $gender;
    public $company;
    public $firstname;
    public $name;
    public $street;
    public $house;
    public $zip;
    public $city;
    public $country = 'DE';
    public $deleted = false;
    public $user_id;

    private $_user = null;
    private $_country = null;

    public function toFormatedString(){
        $ret = '';
        if($this->company){
            $ret .= $this->company . '<br />';
        }
        $ret .= $this->gender . ' ' . $this->firstname . ' ' . $this->name . '<br />' . $this->street . ' ' . $this->house . '<br />' . $this->zip . ' ' . $this->city . '<br />' . $this->getCountry()->name;
        return $ret;
    }

    public function toMailFormatedString(){
        return $this->company . "\r\n" . $this->gender . ' ' . $this->firstname . ' ' . $this->name . "\r\n" . $this->street . ' ' . $this->house . "\r\n" . $this->zip . ' ' . $this->city . "\r\n" . $this->getCountry()->name;
    }

    public function getUser(){
        if(is_null($this->_user)){
            $this->_user = Model_User::find($this->user_id);
        }
        return $this->_user;
    }

    public function getCountry(){
        if(is_null($this->_country)){
            $this->_country = Model_Country::find($this->country);
        }
        return $this->_country;
    }

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Address();
    }

    public static function findByUser($userID, $all = false){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        if(!$all){
            $select->where($table->getTableName() . '.deleted = ?', 'false');
        }

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

    public function delete(){
        $this->deleted = true;
        $this->save();
    }

}
