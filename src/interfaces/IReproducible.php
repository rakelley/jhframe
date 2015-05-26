<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces;

/**
 * Interface for classes of which multiple copies may be needed and a simple
 * method of producing them is desired
 */
interface IReproducible
{

    /**
     * Returns a new instance of implementing class
     * @return object
     */
    public function getNewInstance();
}
