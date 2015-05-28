<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\model;

/**
 * Model trait for models needing to accept parameters and reset stored
 * properties on parameter changes
 */
trait TakesParameters
{
    /**
     * Current model parameters
     * @var array
     */
    protected $parameters = [];


    /**
     * Setter for parameters, resets properties on parameter change
     * 
     * @see \rakelley\jhframe\interfaces\ITakesParameters::setParameters()
     */
    public function setParameters(array $parameters=null)
    {
        if ($this->parameters !== $parameters) {
            $this->parameters = $parameters;
            $this->resetProperties();
        }
    }


    /**
     * Reset stored properties, can be implemented via
     * \rakelley\jhframe\traits\MetaProperties
     * @abstract
     */
    abstract protected function resetProperties();
}
