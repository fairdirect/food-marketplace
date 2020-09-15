<?php

class Model_Region extends Model_ModelAbstract
{
    public $id;
    public $plz;
    public $name;
    public $country;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Region();
    }

    public static function getAll(){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->order('name ASC');
        
        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function getCountriesWithRegions()
    {
        $ret = array();
        $db = self::getDbTable()->getAdapter();
        $stmt = $db->query('SELECT DISTINCT(country) FROM ' . self::getDbTable()->getTableName());
        $countries = $stmt->fetchAll();
        foreach($countries as $country) {
            $ret[] = Model_Country::find($country);
        }
        return $ret;
    }

    public static function getCurrentRegion()
    {
        $session = new Zend_Session_Namespace('Default');
        if ($session->region) {
            return $session->region;
        } else {
        }
        if (Zend_Auth::getInstance()->hasIdentity() && (Zend_Auth::getInstance()->getIdentity())) {
            $region = Zend_Auth::getInstance()->getIdentity()->getRegion();
            return $region;
        }
    }

}
