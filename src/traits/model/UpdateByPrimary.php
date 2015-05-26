<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Standard method for updating all non-primary columns in rows selected by
 * primary index
 */
trait UpdateByPrimary
{

    protected function updateByPrimary(array $values)
    {
        $columns = array_values(array_diff($this->columns,
                                (array) $this->primary));
        $operator = (is_array($this->primary)) ? 'AND' : null;

        $this->db->newQuery('update', $this->table, ['columns' => $columns])
                 ->addWhere()
                 ->Equals($this->primary, $operator)
                 ->makeStatement()
                 ->Bind($this->columns, $values)
                 ->Execute();
    }
}
