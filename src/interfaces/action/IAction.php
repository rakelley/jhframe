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

namespace rakelley\jhframe\interfaces\action;

/**
 * Minimum public interface for executable "actions"
 * API endpoints which perform a specific function, e.g. an internal data
 * update, writing user input, validating user input.
 * Typically (but not always) associated with a controller POST route.
 */
interface IAction
{
    /**
     * Perform defined action
     *
     * @return mixed    Void by default, may optionally return a boolean if
     *                  success of action is conditional
     */
    public function Proceed();


    /**
     * Public getter for error message generated during any process step, if any
     * 
     * @return string    Null if no error
     */
    public function getError();


    /**
     * Public getter for whether the action alters app data when succesfully
     * executed, requiring a cache update/purge
     * 
     * @return boolean
     */
    public function touchesData();
}
