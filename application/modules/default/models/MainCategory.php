<?php

class Model_MainCategory extends Model_ModelAbstract
{
    public $id;
    public $name;

    private $_groups = null;

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

    public function getGroups($onlyBio = false, $onlyDiscount = false, $onlyWholesale = false, $onlyActivated = true, $onlyFromRegion = false){
        if(is_null($this->_groups)){
            $this->_groups = Model_ProductGroup::getByType($this->id, $onlyBio, $onlyDiscount, $onlyWholesale, $onlyActivated, $onlyFromRegion);
        }
        return $this->_groups;
    }

    public function clearGroups(){
        $this->_groups = null;
    }

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_MainCategory();
    }

    public function delete(){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $this->id);
        $table->delete($where);
    }


}
