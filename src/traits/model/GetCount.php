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
 * Standard internal method for getting count of all rows in a table
 */
trait GetCount
{
    /**
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
