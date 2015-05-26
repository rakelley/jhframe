<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\action;

/**
 * Action which generates an additional result to pass
 */
interface IHasResult extends IAction
{

    /**
     * Returns result of successful action for output to user
     * 
     * @return string
     */
    public function getResult();
}
