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
 * Standard method for inserting a single row in a table with an automatic
 * primary key column
 */
trait InsertAutoPrimary
{

    protected function insertAutoPrimary(array $values)
    {
        $columns = array_values(array_diff($this->columns,
                                (array) $this->primary));
        $this->db->newQuery('insert', $this->table, ['columns' => $columns])
                 ->makeStatement()
                 ->Bind($columns, $values)
                 ->Execute();
    }
}
