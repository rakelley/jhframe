<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

/**
 * Trait for classes which need access to the app config store at runtime.
 */
trait ConfigAware
{

    protected function getConfig()
    {
        return \App::getConfig();
    }
}
