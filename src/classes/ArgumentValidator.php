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

namespace rakelley\jhframe\classes;

/**
 * Generic Action that validates user input against a set of arguments
 */
class ArgumentValidator extends Action implements
    \rakelley\jhframe\interfaces\action\IHasResult,
    \rakelley\jhframe\interfaces\ITakesParameters
{
    use \rakelley\jhframe\traits\GetsInput,
        \rakelley\jhframe\traits\ServiceLocatorAware,
        \rakelley\jhframe\traits\TakesParameters;

    protected $touchesData = false;
    protected $validated = [];


    public function Proceed()
    {
        try {
            $this->validated = [];
            if (isset($this->parameters['required'])) {
                $this->validated = $this->getInput($this->parameters['required'],
                                                   $this->parameters['method']);
            }
            if (isset($this->parameters['accepted'])) {
                $this->validated = array_merge(
                    $this->validated,
                    $this->getInput($this->parameters['accepted'],
                                    $this->parameters['method'],
                                    true)
                );
            }
            return true;
        } catch (InputException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }


    public function getResult()
    {
        return $this->validated;
    }
}
