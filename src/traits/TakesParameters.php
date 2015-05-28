<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

/**
 * Default implementation for \rakelley\jhframe\interfaces\ITakesParameters
 */
trait TakesParameters
{
    /**
     * Store for set parameters
     * @var array
     */
    protected $parameters = [];


    /**
     * Setter for parameters
     * 
     * @see \rakelley\jhframe\interfaces\ITakesParameters::setParameters()
     */
    public function setParameters(array $parameters=null)
    {
        $this->parameters = $parameters;
    }
}
