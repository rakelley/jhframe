<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Model trait for common delete operation
 */
trait DeleteByParameter
{

    /**
     * Standard method for deleting rows selected by parameter(s). Defaults to
     * using primary index.
     * 
     * @param  string|null $where Optional column to use in place of primary
     * @return void
     */
    protected function deleteByParameter($where=null)
    {
        $where = ($where) ?: $this->primary;
        $operator = (is_array($where)) ? 'AND' : null;

        $this->db->newQuery('delete', $this->table)
                 ->addWhere()
                 ->Equals($where, $operator)
                 ->makeStatement()
                 ->Bind($where, $this->parameters)
                 ->Execute();
    }
}
