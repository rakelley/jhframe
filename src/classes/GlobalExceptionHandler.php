<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Default implementation for IGlobalExceptionHandler
 */
class GlobalExceptionHandler implements
    \rakelley\jhframe\interfaces\services\IGlobalExceptionHandler
{
    /**
     * IExceptionHandler service instance
     * @var object
     */
    protected $handler;
    /**
     * IIo service instance
     * @var object
     */
    protected $io;


    function __construct(
        \rakelley\jhframe\interfaces\services\IExceptionHandler $handler,
        \rakelley\jhframe\interfaces\services\IIo $io
    ) {
        $this->handler = $handler;
        $this->io = $io;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IGlobalExceptionHandler::Initiate
     */
    public function Initiate(\Exception $e)
    {
        try {
            if (!$this->handler) {
                throw new \RuntimeException('No ExceptionHandler Set');
            }
            $this->handler->Handle($e);
        } catch (\Exception $secondary) {
            $this->handlerFailure($e, $secondary);
        }
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IGlobalExceptionHandler::RegisterSelf
     */
    public function registerSelf()
    {
        set_exception_handler([$this, 'Initiate']);    
    }


    /**
     * If handling the exception via the ExceptionHandler service generates a
     * new exception, this method provides a cruder fallback
     * 
     * @param  object $primary   Initial Exception that we attempted to handle
     * @param  object $secondary Additional Exception raised during handle
     *                           attempt
     * @return void
     */
    protected function handlerFailure(
        \Exception $primary,
        \Exception $secondary
    ) {
            $firstMessage = $primary->getMessage();
            $firstType = get_class($primary);

            $secondType = get_class($secondary);
            $secondContent = $secondary->__toString();

            $error = <<<TEXT
Critical GlobalExceptionHandler Error
   Failed to handle {$firstType} of: "{$firstMessage}".
   Attempt produced secondary {$secondType}
   Content of secondary Exception follows:
   {$secondContent}

TEXT;

        $this->io->toErrorLog($error);
        $this->io->toExit($error);
    }
}
