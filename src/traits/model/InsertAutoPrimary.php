<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Model trait for insertion with an auto key
 */
trait InsertAutoPrimary
{

    /**
     * Inserts a single row in a table with an automatic primary key column
     *
     * @param  array $values Key/value pairs for all non-primary columns
     * @return void
     */
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
