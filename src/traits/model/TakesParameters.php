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

namespace rakelley\jhframe\traits\model;

/**
 * Standard method for models needing to implement
 * \rakelley\jhframe\interfaces\ITakesParameters and reset property state when
 * parameters change.
 * Must use \rakelley\jhframe\traits\MetaProperties or implement resetProperties
 */
trait TakesParameters
{
    /**
     * Current model parameters
     * @var array
     */
    protected $parameters = [];


    public function setParameters(array $parameters=null)
    {
        if ($this->parameters !== $parameters) {
            $this->parameters = $parameters;
            $this->resetProperties();
        }
    }


    abstract protected function resetProperties();
}
