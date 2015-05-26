<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

/**
 * Trait for classes which want to pass an exception on to the Logger service
 */
trait LogsExceptions
{
    protected $loggerInterface = '\rakelley\jhframe\interfaces\services\ILogger';


    /**
     * ServiceLocator dependency, can be resolved by using
     * \rakelley\jhframe\traits\ServiceLocatorAware
     * @abstract
     */
    abstract protected function getLocator();


    /**
     * Provided logging method
     * 
     * @param  object $e     Exception to pass to logger service
     * @param  string $level Level of Exception
     * @return void
     */
    protected function logException(\Exception $e, $level=null)
    {
        $logger = $this->getLocator()->Make($this->loggerInterface);

        $level = ($level) ?: 'unknown';
        $logger->Log($level, $logger->exceptionToMessage($e));
    }
}
