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
 * Interface for class containing a method suitable for registering as the
 * global exception handler.
 */
interface IGlobalExceptionHandler
{
    /**
     * Method to register as global handler.
     * Should attempt to pass the Exception on to the IExceptionHandler service,
     * or halt execution gracefully if doing so generates another Exception.
     * Exceptions *must* not be allowed to escape this method.
     * 
     * @param  object $e Exception to handle
     * @return void
     */
    public function Initiate(\Exception $e);


    /**
     * Register this object's Initiate method as the global exception handler
     *
     * @return void
     */
    public function registerSelf();
}
