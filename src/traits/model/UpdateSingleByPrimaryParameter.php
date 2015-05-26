<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Standard method for updating a single column where the primary index matches
 * a parameter
 */
trait UpdateSingleByPrimaryParameter
{

    /**
     * @param  string $column Column name to update
     * @param  mixed  $value  Value to set $column to
     * @return void
     */
    protected function updateSingleByPrimaryParameter($column, $value)
    {
        $values = [
            $column => $value,
            $this->primary => $this->parameters[$this->primary],
        ];
        $this->db->newQuery('update', $this->table, ['columns' => [$column]])
                 ->addWhere()
                 ->Equals($this->primary)
                 ->makeStatement()
                 ->Bind(array_keys($values), $values)
                 ->Execute();
    }
}
