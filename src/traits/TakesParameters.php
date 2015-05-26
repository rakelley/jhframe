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
    protected $parameters = [];


    public function setParameters(array $parameters=null)
    {
        $this->parameters = $parameters;
    }
}
