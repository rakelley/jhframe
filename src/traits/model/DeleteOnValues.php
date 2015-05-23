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
 * Standard method for deleting rows where column in array of values.
 * Defaults to primary index.
 */
trait DeleteOnValues
{

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