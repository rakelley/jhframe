<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\action;

/**
 * Trait for actions which need to validate a botcheck field
 *
 * Pairs with \rakelley\jhframe\traits\view\HasBotcheckfield
 */
trait ValidatesBotcheckField
{
    protected $botcheckServiceInterface =
        '\rakelley\jhframe\interfaces\services\IBotcheck';


    /**
     * ServiceLocator dependency, can be resolved by using
     * \rakelley\jhframe\traits\ServiceLocatorAware
     * @abstract
     */
    abstract protected function getLocator();


    /**
     * Calls validation method for service
     * 
     * @return void
     */
    protected function validateBotcheckField()
    {
        $this->getLocator()->Make($this->botcheckServiceInterface)
                           ->validateField();
    }
}
