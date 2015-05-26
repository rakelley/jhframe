<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Standard method for selecting the entire contents of a table
 */
trait SelectAll
{

    protected function selectAll()
    {
        $result = $this->db->newQuery('select', $this->table)
                           ->makeStatement()
                           ->FetchAll();

        return ($result) ?: null;
    }
}