<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

/**
 * Standardized trait implementation for singleton pattern
 */
trait Singleton
{
    /**
     * Class instance store
     * @var object|null
     */
    protected static $instance = null;


    /**
     * Gets stored instance of owned class, creating first if necessary
     * 
     * @return object
     * @see \rakelley\jhframe\interfaces\ISingleton::getInstance()
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            $class = get_called_class();
            static::$instance = new $class();
        }

        return static::$instance;
    }
}
