<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces;

/**
 * Interface for classes which only exist to produce class instance of another
 * type
 */
interface IFactory
{

    /**
     * Create and return product object
     * 
     * @return object
     * @throws \RuntimeException if unable to create product
     */
    public function getProduct();
}
