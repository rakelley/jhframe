<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Model trait for getting single rows matching one or more columns
 */
trait SelectOneByParameter
{

    /**
     * Gets first row that matches a column, defaults to primary index
     * 
     * @param  mixed      $where Optional string column name or array of same
     * @return array|null
     */
    protected function selectOneByParameter($where=null)
    {
        $where = ($where) ?: $this->primary;
        $operator = (is_array($where)) ? 'AND' : null;

        $result = $this->db->newQuery('select', $this->table)
                           ->addWhere()
                           ->Equals($where, $operator)
                           ->makeStatement()
                           ->Bind($where, $this->parameters)
                           ->Fetch();

        return ($result) ?: null;
    }
}
