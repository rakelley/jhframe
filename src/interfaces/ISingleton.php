<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces;

/**
 * Interface for singleton-pattern classes
 */
interface ISingleton
{

    /**
     * Gets instance of owning class
     * 
     * @return object
     */
    public static function getInstance();
}
