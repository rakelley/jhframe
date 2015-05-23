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
 * Action which requires a validation step on user input before it can Proceed
 */
interface IRequiresValidation extends IAction
{

    /**
     * Performs sanitizing and validation steps on user input
     * Exceptions thrown must be allowed to bubble up.
     * 
     * @return bool    True if all steps were successful
     */
    public function Validate();
}
