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
 * Default abstract parent class for IAction implementers
 * @abstract
 */
abstract class Action implements \rakelley\jhframe\interfaces\action\IAction
{
    /**
     * Stores reason current action failed, if any
     * @var string
     */
    protected $error = null;
    /**
     * Stores whether performing the action changes app data
     * @var boolean
     */
    protected $touchesData = true;


    /**
     * @see \rakelley\jhframe\interfaces\action\IAction::Proceed
     */
    abstract public function Proceed();


    /**
     * @see \rakelley\jhframe\interfaces\action\IAction::getError
     */
    public function getError()
    {
        return $this->error;
    }


    /**
     * @see \rakelley\jhframe\interfaces\action\IAction::touchesData
     */
    public function touchesData()
    {
        return $this->touchesData;
    }
}
