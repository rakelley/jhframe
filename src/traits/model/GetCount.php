<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Model trait for counting tables
 */
trait GetCount
{

    /**
     * Gets count of all rows in table
     * 
     * @return int
     */
    protected function getCount()
    {
        return $this->db->newQuery('select', $this->table,
                                   ['select' => 'count(*)'])
                        ->stripTicks()
                        ->makeStatement()
                        ->Execute()
                        ->returnStatement()
                        ->fetchColumn();      
    }
}
