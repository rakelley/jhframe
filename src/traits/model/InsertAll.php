<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Model trait for inserting rows without auto key
 */
trait InsertAll
{

    /**
     * Standard method for inserting a new row with values defined for all
     * columns.
     * The keys of $values are assumed to at least contain the full set of
     * columns.
     * Extraneous keys are safely ignored.
     *
     * @param  array $values Key/value pairs for all columns
     * @return void
     */
    protected function insertAll(array $values)
    {
        $this->db->newQuery('insert', $this->table,
                            ['columns' => $this->columns])
                 ->makeStatement()
                 ->Bind($this->columns, $values)
                 ->Execute();
    }
}
