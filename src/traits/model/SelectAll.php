<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Model trait for getting entire table
 */
trait SelectAll
{

    /**
     * Gets all rows in table
     * 
     * @return array|null
     */
    protected function selectAll()
    {
        $result = $this->db->newQuery('select', $this->table)
                           ->makeStatement()
                           ->FetchAll();

        return ($result) ?: null;
    }
}