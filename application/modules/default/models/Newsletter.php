<?php

class Model_Newsletter extends Model_ModelAbstract
{
    public static $recipients = array('ALL' => 'Alle', 'ACTIVATED_SHOPS' => 'Aktivierte Shops', 'ALL_SHOPS' => 'Alle Shops', 'ALL_CUSTOMERS' => 'Alle Kunden', 'SINGLE_ADDRESS' => 'Einzelne Adresse');
    public static $vars = array('#USERNAME#');

    public $id;
    public $subject;
    public $content;
    public $created;
    public $type;

    private $_logs = null;
 
    public function __construct($data)
    {
        parent::init($data);
    }

    public static function getDbTable(){
        return new Model_DbTable_Newsletter();
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

    public function writeLog($address, $success){
        $db = self::getDbTable()->getAdapter();
        $db->query("INSERT INTO epelia_newsletters_log(newsletter_id, address, success) VALUES(?, ?, ?)", array($this->id, $address, ($success) ? 't' : 'f'));
    }

    public function getLogs(){
        if(is_null($this->_logs)){
            $ret = array();
            $db = self::getDbTable()->getAdapter();
            $this->_logs = $db->query("SELECT * FROM epelia_newsletters_log WHERE newsletter_id = ?")->order('sent_date DESC')->order('id DESC')->execute($this->id);
        }
        return $this->_logs;
    }

    public function lastSent(){
        $logs = $this->getLogs();
        return (empty($logs)) ? false : $logs[0]['sent_date'];
    }
  
}
