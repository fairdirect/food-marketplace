<?php
abstract class Model_ModelAbstract
{
    /**
     * Initializes common functionality in Model classes
     */
    public function init($data = array())
    {
        foreach($data as $key => $val){
            if(property_exists($this, $key)){
                $this->$key = $val;
            }
        }
    }

    public function toArray(){
        $ret = array();
        foreach($this as $key => $value){
            if($value === 'NULL'){
                $value = null;
            }
            if(is_bool($value)){
                $value = ($value) ? 't' : 'f';
            }
            $ret[$key] = $value;
        }
        return $ret;
    }

    public function toFormArray(){
        $ret = array();
        foreach($this as $key => $value){
            if($value === 'NULL'){
                $value = null;
            }
            if(is_bool($value)){
                $value = ($value) ? '1' : '0';
            }
            $ret[$key] = $value;
        }
        return $ret;
    }


    public function save(){
        if(property_exists($this, 'created')){
            unset($this->created);
        }
        if($this->id){
            $this->getDbTable()
                ->update($this->toArray(), array('id = ?' => $this->id));
        }
        else{
            $this->getDbTable()->insert($this->toArray());
            $this->id = $this->getDbTable()->getAdapter()->lastSequenceId($this->getDbTable()->getTableName() . '_id_seq');
        }
    }
    
    /**
     * Returns the primary key column name
     *
     * @see Model_DbTable_TableAbstract::getPrimaryKeyName()
     * @return string|array The name or array of names which form the primary key
     */
    public static function getPrimaryKeyName()
    {
        return static::getDbTable()->getPrimaryKeyName();
    }
 
    /**
     * Returns the name of the table that this model represents
     *
     * @return string
     */
    public function getTableName() {
        return self::getDbTable()->getTableName();
    }

    public static function find($primary_key)
    {
        $table = static::getDbTable();
        $select = $table->select();

        $select->where(static::getPrimaryKeyName() . ' = ?', $primary_key);
        
        $result = $table->fetchRow($select);

        if (is_null($result)) {
            return null;
        }
        $model = new static($result);
        return $model;
    }

    public function fetchAll()
    {
        $resultSet = self::getDbTable()->fetchAll();
        $entries = array();
        foreach ($resultSet as $row) {
            $entry = new self($row);
            $entries[] = $entry;
        }
        return $entries;
    }

}
