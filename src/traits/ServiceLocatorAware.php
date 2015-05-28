<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

/**
 * Trait for classes which need access to the ServiceLocator at runtime
 */
trait ServiceLocatorAware
{

    /**
     * Returns the IServiceLocator object associated with the current App
     * 
     * @return \rakelley\jhframe\interfaces\services\IServiceLocator
     */
    protected function getLocator()
    {
        return \App::getLocator();
    }
}
