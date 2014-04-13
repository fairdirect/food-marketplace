<?php
/**
 * Application Model DbTables
 *
 * @package Model
 * @subpackage DbTable
 * @author <YOUR NAME HERE>
 * @copyright ZF model generator
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Abstract class that is extended by all tables
 *
 * @package Model
 * @subpackage DbTable
 * @author <YOUR NAME HERE>
 */
abstract class Model_DbTable_TableAbstract extends Zend_Db_Table_Abstract
{
    /**
     * $_name - Name of database table
     *
     * @return string
     */
    protected $_name;

    /**
     * $_id - The primary key name(s)
     *
     * @return string|array
     */
    protected $_id;

    /**
     * Returns the primary key column name(s)
     *
     * @return string|array
     */
    public function getPrimaryKeyName()
    {
        return $this->_id;
    }

    /**
     * Returns the table name
     *
     * @return string
     */
    public function getTableName()
    {
        return $this->_name;
    }

    /**
     * Returns the number of rows in the table
     *
     * @return int
     */
    public function countAllRows()
    {
        $query = $this->select()->from($this->_name, 'count(*) AS all_count');
        $numRows = $this->fetchRow($query);

        return $numRows['all_count'];
    }

    /**
     * Returns the number of rows in the table with optional WHERE clause
     *
     * @param $where mixed Where clause to use with the query
     * @return int
     */
    public function countByQuery($where = '')
    {
        $query = $this->select()->from($this->_name, 'count(*) AS all_count');

		if (! empty($where) && is_string($where))
        {
            $query->where($where);
        }
        elseif(is_array($where) && isset($where[0]))
        {
			foreach($where as $i => $v)
			{
				/**
				 * Checks if you're passing an PDO escape statement
				 * ->where('price > ?', $price)
				 */
				if(isset($v[1]) && is_string($v[0]) && count($v) == 2)
				{
					$query->where($v[0], $v[1]);
				}
				elseif(is_string($v))
				{
					$query->where($v);
				}
			}
        }
        else
        {
            throw new Exception("You must pass integer indexes on the select statement array.");
        }


        $row = $this->getAdapter()->query($query)->fetch();

        return $row['all_count'];
    }

}
