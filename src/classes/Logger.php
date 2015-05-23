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

namespace rakelley\jhframe\classes;

use Psr\Log\LogLevel;

/**
 * Default implementation of ILogger
 */
class Logger implements \rakelley\jhframe\interfaces\services\ILogger
{
    use \rakelley\jhframe\traits\ConfigAware,
        \rakelley\jhframe\traits\GetsServerProperty,
        \Psr\Log\LoggerTrait;

    /**
     * Path to log file for critical-level messages
     * @var string
     */
    protected $criticalLog;
    /**
     * Path to default log file
     * @var string
     */
    protected $defaultLog;
    /**
     * IFileSystemAbstractor instance
     * @var object
     */
    protected $fileSystem;
    /**
     * Path to log file for info-level messages
     * @var string
     */
    protected $infoLog;
    /**
     * Path to log file for user-level messages
     * @var string
     */
    protected $userLog;


    function __construct(
        \rakelley\jhframe\interfaces\services\IFileSystemAbstractor $fileSystem
    ) {
        $logDir = $this->getConfig()->Get('ENV', 'log_dir');

        $default = $this->getConfig()->Get('ENV', 'log_default');
        $this->defaultLog = $logDir . $default;

        $critical = $this->getConfig()->Get('ENV', 'log_critical');
        $this->criticalLog = $logDir . (($critical) ?: $default);

        $user = $this->getConfig()->Get('ENV', 'log_user');
        $this->userLog = $logDir . (($user) ?: $default);

        $info = $this->getConfig()->Get('ENV', 'log_info');
        $this->infoLog = $logDir . (($info) ?: $default);

        $this->fileSystem = $fileSystem;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\ILogger::exceptionToMessage
     */
    public function exceptionToMessage(\Exception $e)
    {
        $type = get_class($e);
        $message = $e->getMessage();
        $trace = implode(",\n    ", array_map(
            function($t) {
                $line = "{$t['function']}";
                if (!empty($t['line'])) {
                    $line .= ", Line: {$t['line']}";
                }
                if (!empty($t['class'])) {
                    $line =  "{$t['class']}::" . $line;
                }
                return $line;
            },
            $e->getTrace()
        ));
        $file = $e->getFile();
        $line = $e->getLine();
        $route = $this->getRoute();

        $entry = <<<TEXT
{$type} Exception
    Error:
    {$message}

    Trace:
    {$trace}

    Origin:
    {$file} at {$line}

    Route:
    {$route}
TEXT;

        return $entry;
    }


    /**
     * @see \Psr\Log\LoggerInterface::Log
     */
    public function Log($level, $message, array $context=array())
    {
        switch ($level) {
            case LogLevel::EMERGENCY:
                $log = $this->criticalLog;
                break;

            case LogLevel::ALERT:
                $log = $this->criticalLog;
                break;

            case LogLevel::CRITICAL:
                $log = $this->criticalLog;
                break;

            case LogLevel::ERROR:
                $log = $this->criticalLog;
                break;

            case LogLevel::WARNING:
                $log = $this->userLog;
                break;

            case LogLevel::NOTICE:
                $log = $this->infoLog;
                break;

            case LogLevel::INFO:
                $log = $this->infoLog;
                break;

            case LogLevel::DEBUG:
                $log = $this->infoLog;
                break;

            default:
                $log = $this->defaultLog;
                $level = 'unknown';
                break;
        }

        $this->writeTo($log, $level, $message, $context);
    }


    /**
     * Internal method for writing to a log.
     * Will optionally interpolate context according to PSR-3 standard.
     * 
     * @param  string $log     Absolute path to log file to write to
     * @param  string $level   Message level
     * @param  string $message Message to write (with possible modification)
     * @param  array  $context Context for message, accepts key/value pairs to
     *                         interpolate with message
     * @return void
     */
    protected function writeTo($log, $level, $message, array $context)
    {
        if ($context) {
            $message = $this->interpolateMessage($message, $context);
        }

        $date = date(\DateTime::ATOM);
        $entry = <<<TEXT
[{$date}] {$level}-Level Log Entry
    {$message}


TEXT;

        $this->fileSystem->getFileWithPath($log)->Append($entry);
    }


    /**
     * Reference implementation of message interpolating
     * @link https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md#12-message
     */
    protected function interpolateMessage($message, array $context)
    {
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        return strtr($message, $replace);
    }


    /**
     * Determines current route based on server values if set
     * 
     * @return string
     */
    protected function getRoute()
    {
        if ($this->getServerProp('REQUEST_URI')) {
            return $this->getServerProp('REQUEST_METHOD') . ': ' .
                   $this->getServerProp('REQUEST_URI');
        } else {
            return '';
        }
    }
}
