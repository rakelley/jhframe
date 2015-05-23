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
 * Interface for custom exception handlers
 */
interface IExceptionHandler
{
    /**
     * Constants used by bootstrap environmental config to set logging level
     *
     * none   - no exceptions logged
     * system - internal exceptions only, no exceptions based on user input
     * all    - all exceptions logged
     */
    const LOGGING_NONE = 10;
    const LOGGING_SYSTEM = 110;
    const LOGGING_ALL = 120;

    
    /**
     * Handle Exception and then terminate execution.
     * Should render an error view to the user or render an API action failed
     * object as appropriate.
     *
     * @return void
     */
    public function Handle(\Exception $e);
}
