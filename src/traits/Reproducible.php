<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

/**
 * Default implementation for \rakelley\jhframe\interfaces\IReproducible
 */
trait Reproducible
{

    /**
     * ServiceLocator dependency, can be resolved by using ServiceLocatorAware
     * @see \rakelley\jhframe\traits\ServiceLocatorAware
     * @abstract
     */
    abstract protected function getLocator();


    /**
     * @see \rakelley\jhframe\interfaces\IReproducible::getNewInstance
     */
    public function getNewInstance()
    {
        return $this->getLocator()->getNew($this);
    }
}
