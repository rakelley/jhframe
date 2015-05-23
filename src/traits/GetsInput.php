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

namespace rakelley\jhframe\traits;

/**
 * Trait for classes which need a standardized way to get user input, which may
 * optionally already exist in class parameters.
 */
trait GetsInput
{
    protected $inputServiceInterface =
        '\rakelley\jhframe\interfaces\services\IInput';


    /**
     * ServiceLocator dependency, can be resolved by using
     * \rakelley\jhframe\traits\ServiceLocatorAware
     * @abstract
     */
    abstract protected function getLocator();


    /**
     * Class method for getting user input.  Matches against class parameters,
     * if any, before calling to input service.
     * 
     * @param  array   $list     keys and rules to get,
     *                           @see \rakelley\jhframe\interfaces\services\IInput::Get
     * @param  string  $method   HTTP method expected
     * @param  boolean $optional true if input is accepted but not required
     * @return array             Gathered input
     */
    protected function getInput(array $list, $method, $optional=false)
    {
        $input = [];

        // class parameters property is optional
        if (!empty($this->parameters)) {
            $matches = array_intersect(array_keys($list),
                                       array_keys($this->parameters));
            if ($matches) {
                array_walk(
                    $matches,
                    function($k) use (&$input, &$list) {
                        if (isset($this->parameters[$k]) &&
                            $this->parameters[$k] !== ''
                        ) {
                            $input[$k] = $this->parameters[$k];
                            unset($list[$k]);
                        }
                    }
                );
                if (!$list) {
                    return $input;
                }
            }
        }
        
        $fetched = $this->getLocator()->Make($this->inputServiceInterface)
                                      ->getList($list, $method, $optional);
        $input = array_merge($input, $fetched);

        return $input;
    }
}
