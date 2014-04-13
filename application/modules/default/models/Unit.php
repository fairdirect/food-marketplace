<?php

class Model_Unit extends Model_ModelAbstract
{
    public $id;
    public $singular;
    public $plural;
 
    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Unit();
    }

    public function delete(){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('id = ?', $this->id);
        $table->delete($where);
    }

    public static function getAll(){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->order('singular DESC');

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
