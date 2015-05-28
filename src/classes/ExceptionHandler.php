<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

use Psr\Log\LogLevel;

/**
 * Exception handling library to deal with logging and creating appropriate
 * output for the user.
 * Constants are for severity of current exception, used in conjunction with
 * logging level to determine if exception should be logged.
 */
class ExceptionHandler implements
    \rakelley\jhframe\interfaces\services\IExceptionHandler
{
    use \rakelley\jhframe\traits\ConfigAware,
        \rakelley\jhframe\traits\LogsExceptions,
        \rakelley\jhframe\traits\ServiceLocatorAware;

    /**
     * If current HTTP request is an API call
     * @var boolean
     */
    protected $apiCall;
    /**
     * If current defined environment is development
     * @var bool
     */
    protected $devEnv;
    /**
     * Instance of Exception currently being addressed
     * @var \Exception
     */
    protected $e;
    /**
     * Path to error view file
     * @var string
     */
    protected $errorView;
    /**
     * IFileSystemAbstractor instance
     * @var object
     */
    protected $fileSystem;
    /**
     * IIo service instance
     * @var object
     */
    protected $io;
    /**
     * Environment-defined threshold of logging
     * @var int
     */
    protected $logLevel;
    /**
     * API Result Container instance to use if handling an API call
     * @var object
     */
    protected $resultContainer;
    /**
     * Severity of current exception
     * @var int
     */
    protected $severity;
    /** user-level error with code 4xx */
    const SEVERITY_USER = 100;
    /** unanticipated error with unknown severity */
    const SEVERITY_UNKNOWN = 110;
    /** system-level error with code 5xx */
    const SEVERITY_SYSTEM = 120;


    /**
     * @param \rakelley\jhframe\classes\resources\ActionResult            $resultContainer
     * @param \rakelley\jhframe\interfaces\services\IFileSystemAbstractor $fileSystem
     * @param \rakelley\jhframe\interfaces\services\IIo                   $io
     */
    function __construct(
        \rakelley\jhframe\classes\resources\ActionResult $resultContainer,
        \rakelley\jhframe\interfaces\services\IFileSystemAbstractor $fileSystem,
        \rakelley\jhframe\interfaces\services\IIo $io
    ) {
        $this->resultContainer = $resultContainer;
        $this->fileSystem = $fileSystem;
        $this->io = $io;

        $apiCall = $this->getConfig()->Get('ENV', 'is_ajax');
        $this->apiCall = $apiCall;

        $devEnv = ($this->getConfig()->Get('ENV', 'type') === 'development');
        $this->devEnv = $devEnv;

        $errorView = $this->getConfig()->Get('ENV', 'public_dir') .
                     $this->getConfig()->Get('APP', 'error_view');
        $this->errorView = $errorView;

        $logLevel = $this->getConfig()->Get('APP', 'exception_log_level');
        if (!isset($logLevel)) {
            $logLevel = $this::LOGGING_ALL;
        }
        $this->logLevel = $logLevel;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IExceptionHandler::Handle()
     */
    public function Handle(\Exception $e)
    {
        $this->e = $e;
        $this->severity = $this->getSeverityByCode($this->e->getCode());

        $this->log();

        if ($this->apiCall) {
            $this->renderError();
        } else {
            $this->serveView();
        }

        $this->io->toExit();
    }


    /**
     * Conditionally logs exceptions based on severity and logging threshold
     * 
     * @return void
     */
    protected function Log()
    {
        if ($this->severity >= $this::SEVERITY_SYSTEM &&
            $this->logLevel >= $this::LOGGING_SYSTEM
        ) {
            $level = LogLevel::CRITICAL;
        } elseif ($this->severity <= $this::SEVERITY_USER && 
                  $this->logLevel >= $this::LOGGING_ALL
        ) {
            $level = LogLevel::WARNING;
        } elseif ($this->severity > $this::SEVERITY_USER && 
                  $this->logLevel > $this::LOGGING_NONE) {
            $level = 'Unknown';
        } else {
            return;
        }

        $this->logException($this->e, $level);
    } 


    /**
     * Determines severity of current exception by code type
     * 
     * @param  int|string $code Code of current exception
     * @return int              Corresponding class constant
     */
    protected function getSeverityByCode($code)
    {
        $codeType = substr($code, 0, 1);

        switch ($codeType) {
            case '4':
                return $this::SEVERITY_USER;
                break;

            case '5':
                return $this::SEVERITY_SYSTEM;
                break;
            
            default:
                return $this::SEVERITY_UNKNOWN;
                break;
        }
    }


    /**
     * Serves error view
     * 
     * @return void
     */
    protected function serveView()
    {
        if ($this->devEnv) {
            $view = $this->e->getMessage();
        } else {
            $code = $this->e->getCode();
            if ($code) {
                $this->io->httpCode($code);
                $parameters = ['code' => $code];
            } else {
                $parameters = null;
            }

            $view = $this->fileSystem->containeredInclude($this->errorView,
                                                          $parameters);
        }

        $this->io->toEcho($view);
    }


    /**
     * Outputs failure via API result container
     * 
     * @return void
     */
    protected function renderError()
    {
        if ($this->devEnv || $this->severity <= $this::SEVERITY_USER) {
            $error = $this->e->getMessage();
        } else {
            $error = 'An Internal Error Occurred';
        }

        $this->resultContainer->getNewInstance()
                              ->setSuccess(false)
                              ->setError($error)
                              ->Render();
    }
}
