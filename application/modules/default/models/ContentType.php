<?php

class Model_ContentType extends Model_ModelAbstract
{
    public $id;
    public $name;
    public $base_unit;
    public $base_factor;
    public $deleted = false;
 
    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_ContentType();
    }

    public function delete(){
        $this->deleted = true;
        $this->save();
    }

    public static function getAll($all = false){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        if($all){
            $select->where($table->getTableName() . '.deleted', true);
        }

        $select->order('name DESC');

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
