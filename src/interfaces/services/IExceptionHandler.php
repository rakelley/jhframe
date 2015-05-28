<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Interface for custom exception handlers.
 * Constants are used by bootstrap ENV config to set logging level
 */
interface IExceptionHandler
{
    /** no exceptions logged */
    const LOGGING_NONE = 10;
    /** internal exceptions only, no exceptions based on user input */
    const LOGGING_SYSTEM = 110;
    /** all exceptions logged */
    const LOGGING_ALL = 120;

    
    /**
     * Handle Exception and then terminate execution.
     * Should render an error view to the user or render an API action failed
     * object as appropriate.
     *
     * @param \Exception $e Exception to handle
     * @return void
     */
    public function Handle(\Exception $e);
}
