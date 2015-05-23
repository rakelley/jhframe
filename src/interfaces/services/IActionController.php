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

namespace rakelley\jhframe\interfaces\services;

/**
 * Controller class for Actions, steps through their public methods and stores
 * the result in a container
 */
interface IActionController
{

    /**
     * Create and execute Action, store result in container object and return.
     * Execution must be aware of all possible Action interfaces.
     * 
     * @param  string $actionName Qualified class name of Action
     * @param  array  $parameters Optional parameters to pass to Action
     * @return object             \rakelley\jhframe\classes\resources\ActionResult
     */
    public function executeAction($actionName, array $parameters=null);
}
