<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\view;

/**
 * Trait for public-facing FormViews which need a botcheck field
 *
 * Pairs with \rakelley\jhframe\traits\action\ValidatesBotcheckField
 */
trait HasBotcheckField
{
    /**
     * Interface for dependent service
     * @var string
     */
    protected $botcheckServiceInterface =
        '\rakelley\jhframe\interfaces\services\IBotcheck';


    /**
     * ServiceLocator dependency, can be resolved by using
     * \rakelley\jhframe\traits\ServiceLocatorAware
     * @abstract
     */
    abstract protected function getLocator();


    /**
     * Calls field getter method for service
     *
     * @return string
     */
    protected function addBotcheckField()
    {
        return $this->getLocator()->Make($this->botcheckServiceInterface)
                                  ->getField();
    }
}
