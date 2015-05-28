<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Model trait for common way of deleting rows matching a set
 */
trait DeleteOnValues
{

    /**
     * Standard method for deleting rows where column in array of values.
     * Column defaults to primary index.
     *
     * @param  array       $values List of values to match against
     * @param  string|null $column Column to match with $values
     * @return void
     */
    protected function deleteOnValues(array $values, $column=null)
    {
        //ensure indices are contiguous
        $values = array_values($values);
        $column = ($column) ?: $this->primary;

        $this->db->newQuery('delete', $this->table)
                 ->addWhere()
                 ->In($column, $values)
                 ->makeStatement()
                 ->Execute($values);
    }
}
