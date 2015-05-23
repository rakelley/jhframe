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

namespace rakelley\jhframe\interfaces\repository;

/**
 * Repository which has a method for getting a random assortment of its
 * collection members
 */
interface IRandomAccess
{
    
    /**
     * Get a random number of members.
     * If the total number of members of the repo's collection is less than
     * $count, all members should be returned in a random order instead,
     * without raising an exception.
     * 
     * @param  int   $count Number of members to get
     * @return array        Randomly fetched members
     */
    public function getRandom($count);
}
