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

namespace rakelley\jhframe\traits;

/**
 * Standardized trait implementation for \rakelley\jhframe\interfaces\ISingleton
 */
trait Singleton
{
    protected static $instance = null;


    public static function getInstance()
    {
        if (!static::$instance) {
            $class = get_called_class();
            static::$instance = new $class();
        }

        return static::$instance;
    }
}
