<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Standard method for inserting a new row with values defined for all columns.
 * 
 * The keys of $values are assumed to at least contain the full set of columns.
 * Extraneous keys are safely ignored.
 */
trait InsertAll
{

    protected function insertAll(array $values)
    {
        $this->db->newQuery('insert', $this->table,
                            ['columns' => $this->columns])
                 ->makeStatement()
                 ->Bind($this->columns, $values)
                 ->Execute();
    }
}
