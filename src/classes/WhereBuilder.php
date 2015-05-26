<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

use \rakelley\jhframe\interfaces\services\IDatabase;

/**
 * Default implementation of IDatabaseWhereBuilder
 */
class WhereBuilder implements
    \rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder
{
    /**
     * IDatabase instance
     * @var object
     */
    private $db = null;
    /**
     * Current where statement
     * @var string
     */
    private $where = '';


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder::newWhere
     */
    public function newWhere(IDatabase $db, $operator=null)
    {
        $this->db = $db;
        if (!isset($operator)) $operator = 'WHERE';

        $this->where = " $operator ";
        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder::Equals
     */
    public function Equals($column, $operator=null)
    {
        if (is_array($column)) {
            $column = array_values($column); //ensure sequential indexes
            $last = count($column)-1;
            array_walk(
                $column,
                function($value, $index) use ($last, $operator) {
                    if ($index === $last) {
                        $operator = null;
                    }
                    $this->Equals($value, $operator);
                }
            );
        } else {
            $this->where .= "`$column`=:$column";
            if ($operator) $this->where .= " $operator ";
        }

        return $this->completeStatement();
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder::In
     */
    public function In($column, array $values)
    {
        $placeholder = implode(',', array_fill(0, count($values), '?'));
        $this->where .= "`$column` IN (" . $placeholder . ")";

        return $this->completeStatement();
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder::Like
     */
    public function Like($column, $placeholder, $operator=null)
    {
        if (is_array($placeholder)) {
            $last = count($placeholder)-1;
            array_walk(
                $placeholder,
                function($value, $index) use ($column, $last, $operator) {
                    if ($index === $last) {
                        $operator = null;
                    }
                    $this->Like($column, $value, $operator);
                }
            );
        } else {
            $this->where .= "`$column` LIKE :$placeholder";
            if ($operator) $this->where .= " $operator ";
        }

        return $this->completeStatement();
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder::isNotNull
     */
    public function isNotNull($column)
    {
        $this->where .= "`$column` IS NOT NULL";

        return $this->completeStatement();
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder::isNull
     */
    public function isNull($column)
    {
        $this->where .= "`$column` IS NULL";

        return $this->completeStatement();
    }


    /**
     * Reusable internal method for completing statement
     * 
     * @return object \rakelley\jhframe\interfaces\services\IDatabase
     */
    protected function completeStatement()
    {
        $this->db->Append($this->where);
        $this->where = '';
        return $this->db;
    }
}
