<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

/**
 * Trait abstracting calls to $_SERVER
 */
trait GetsServerProperty
{

    /**
     * @param  string $key Array key
     * @return mixed
     */
    protected function getServerProp($key)
    {
        return (isset($_SERVER[$key])) ? $_SERVER[$key] : null;
    }
}
