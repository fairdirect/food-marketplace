<?php

class Model_Email extends Model_ModelAbstract
{

    public $name;
    public $subject;
    public $content;
    public $vars;

    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Email();
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

    public function update(){
        $this->getDbTable()
            ->update($this->toArray(), array('name = ?' => $this->name));
    }
 
}
