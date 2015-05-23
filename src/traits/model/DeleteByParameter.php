<?php
/**
 * @package jhframe
 * 
 * All content covered under The MIT License except where included 3rd-party
 * vendor files are licensed otherwise.
 * 
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Standard method for deleting rows selected by parameter(s). Defaults to using
 * primary index.
 */
trait DeleteByParameter
{

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
