<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

/**
 * Utility trait providing standardized way of getting class namespace from
 * within a trait or parent classes method, as __NAMESPACE__ will be wrong.
 */
trait GetCalledNamespace
{

    /**
     * Returns namespace of class in which method is called
     * 
     * @return string
     */
    public function getCalledNamespace()
    {
        return substr(
            get_called_class(),
            0,
            strrpos(get_called_class(), '\\')
        );
    }
}
