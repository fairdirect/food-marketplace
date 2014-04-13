<?php

class Model_ProductPrice extends Model_ModelAbstract
{
    public $id;
    public $product_id;
    public $value;
    public $quantity;
    public $contents;
    public $is_wholesale = false;
    public $deleted = false;
    public $unit_type_id;
    public $content_type_id;
    public $active = true;

    private $_unit_type;
    private $_content_type;

    public function __construct($data)
    {
        parent::init($data);
    }

    public function getBasePriceString(){
        if(!$this->getContentType()->base_unit){
            return number_format($this->value, 2, ',', '') . ' EUR / ' . $this->getContentType()->name;
        }
        return number_format($this->value / $this->quantity * ($this->getContentType()->base_factor / $this->contents), 2, ',', '') . ' EUR / ' . $this->getContentType()->base_unit;
    }

    public function getUnitType(){
        if(is_null($this->_unit_type)){
            $this->_unit_type = Model_Unit::find($this->unit_type_id);
        }
        return $this->_unit_type;
    }

    public function getContentType(){
        if(is_null($this->_content_type)){
            $this->_content_type = Model_ContentType::find($this->content_type_id);
        }
        return $this->_content_type;
    }

    public static function getDbTable(){
        return new Model_DbTable_ProductPrice();
    }

    public static function findByProduct($productID, $onlyActive = false){
        $table = self::getDbTable();
        $select = $table->select();
        $ret = array();

        $select->where('product_id = ?', $productID);

        if($onlyActive){
            $select->where('active');
        }

        $select->order('is_wholesale DESC');
        $select->order('value ASC');
        
        $result = $table->fetchAll($select);
        if (is_null($result)) {
            return array();
        }
        foreach($result as $r){
            $ret[] = new self($r);
        }
        return $ret;
    }

    public static function deleteForProduct($productID){
        $table = self::getDbTable();
        $where = $table->getAdapter()->quoteInto('product_id = ?', $productID);
        try{ // try deleting the price, will fail if price is referenced by orders
            $table->delete($where);
        }
        catch(Exception $e){ // if price is referenced, set inactive
            $table->update(array('active' => 'f'), $where);
        }
    }
}
