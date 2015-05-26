<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Interface for logging service
 */
interface ILogger extends \Psr\Log\LoggerInterface
{

    /**
     * Converts an Exception into a standardized log message
     * 
     * @param  object $e Exception to convert
     * @return string    Message to pass to log method
     */
    public function exceptionToMessage(\Exception $e);
}
