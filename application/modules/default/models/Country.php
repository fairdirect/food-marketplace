<?php

class Model_Country extends Model_ModelAbstract
{
    public $id;
    public $name;
    public $phone;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Country();
    }

    public static function getAll(){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

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

    public function save(){
        $country = self::find($this->id);
        if($country){
            $this->getDbTable()
                ->update($this->toArray(), array('id = ?' => $this->id));
        }
        else{
            $this->getDbTable()->insert($this->toArray());
        }
    }


}
