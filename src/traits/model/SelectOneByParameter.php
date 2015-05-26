<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Standard method for fetching a single row matched by index column(s) via
 * parameters.  Defaults to using primary index.
 */
trait SelectOneByParameter
{

    /**
     * @param  mixed $where Optional string column name or array of same
     * @return array
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
