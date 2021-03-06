<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces;

/**
 * Interface for classes which take a set of parameters
 */
interface ITakesParameters
{
    
    /**
     * Set list of parameters for a class
     * 
     * @param  array|null $parameters
     * @return void
     */
    public function setParameters(array $parameters=null);
}
