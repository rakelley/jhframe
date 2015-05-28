<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Class for bootstrapping application, should be created and called by
 * index.php (or equivalent) and any scripts
 */
class Bootstrapper
{
    /**
     * Application class, optionally set by ::handleArgs
     * @var string
     */
    protected $appClass = '\rakelley\jhframe\classes\App';
    /**
     * Name of application, set by ::Bootstrap
     * @var string
     */
    protected $appName;
    /**
     * IConfig class name to pass to App constructor as override for default.
     * Optionally set by ::handleArgs
     * @var string|null
     * @see \rakelley\jhframe\classes\App::__construct()
     */
    protected $configClassOverride = null;
    /**
     * Current environment type, set by ::handleArgs
     * @var string
     */
    protected $environment;
    /**
     * IServiceLocator class name to pass to App constructor as override for
     * default.
     * Optionally set by ::handleArgs
     * @var string|null
     * @see \rakelley\jhframe\classes\App::__construct()
     */
    protected $locatorClassOverride = null;
    /**
     * Absolute path to root directory, set by constructor
     * @var string
     */
    protected $rootDir;


    function __construct()
    {
        $this->rootDir = $this->getRootConstant();
        if (!$this->rootDir) {
            throw new \RuntimeException(
                'Required Root Directory Constant Not Defined',
                500
            );
        }
    }


    /**
     * Bootstrap application by name, optionally using args
     * 
     * @param  string     $appName Name of application to load
     * @param  array|null $args    Optional arguments
     *     string 'appClass'     Fully qualified name of application class to
     *                           use instead of default
     *     string 'configClass'  Fully qualified name of IConfig class to pass
     *                           to App constructor
     *     string 'locatorClass' Fully qualified name of IServiceLocator class
     *                           to pass to App constructor
     *     string 'environment'  Specify current environment instead of deriving
     *                           from available config files
     * @return object              App class instance
     */
    public function Bootstrap($appName, array $args=null)
    {
        $this->appName = $appName;

        $this->handleArgs($args);

        $app = $this->makeApp($this->appClass, [$this->configClassOverride,
                                                $this->locatorClassOverride]);

        $this->loadConfig($app->config);

        $app->setClassListFromConfig();
        $app->registerExceptionHandler();

        return $app;
    }


    /**
     * Handle optional args passed to Bootstrap and setup class properties
     * 
     * @param  array|null $args @see ::Boostrap
     * @return void
     * @throws \RuntimeException if no root directory has been defined via
     *                           constant or arg
     */
    protected function handleArgs(array $args=null)
    {
        if (isset($args['appClass'])) {
            $this->appClass = $args['appClass'];
        }

        if (isset($args['configClass'])) {
            $this->configClassOverride = $args['configClass'];
        }

        if (isset($args['locatorClass'])) {
            $this->locatorClassOverride = $args['locatorClass'];
        }

        $this->environment = (isset($args['environment'])) ?
                             $args['environment'] : $this->deriveEnvironment();      
    }


    /**
     * Derive environment based on available app config files.  Avoids having
     * to manually specify environment, just remove non-production files in
     * production.
     * 
     * @return string
     * @throws \RuntimeException if unsuccessful
     */
    protected function deriveEnvironment()
    {
        $confDir = $this->getAppConfigDir();

        if (file_exists($confDir . 'development.php')) {
            return 'development';
        } elseif (file_exists($confDir . 'testing.php')) {
            return 'testing';
        } elseif (file_exists($confDir . 'production.php')) {
            return 'production';
        } else {
            throw new \RuntimeException(
                'Environment Not Specified And Unable To Derive',
                500
            );
        }
    }


    /**
     * Load all config files and finish setting up environment
     *
     * @param  object $configStore IConfig instance from app being bootstrapped
     * @return void
     */
    protected function loadConfig(
        \rakelley\jhframe\interfaces\services\IConfig $configStore
    ) {
        // Get base framework config
        $basePath = $this->getFrameworkConfig();
        $baseConfig = require($basePath);
        array_walk($baseConfig, [$configStore, 'Merge']);

        // Get application config
        $appPath = $this->getAppConfigDir() . $this->environment . '.php';
        $appConfig = require($appPath);
        array_walk($appConfig, [$configStore, 'Merge']);

        // Set PHP runtime values
        $this->configurePhp($configStore->Get('PHP'));
    }


    /**
     * Handle runtime configuration of PHP ini values, if any specified
     *
     * @param  array|null $config Key/value pairs to set, if any
     * @return void
     */
    protected function configurePhp(array $config=null)
    {
        if (!$config) {
            return;
        }

        array_walk(
            $config,
            function($value, $key) {
                if ($key === 'timezone') {
                    $this->setTimezone($value);
                } else {
                    $this->setIniValue($key, $value);
                }
            }
        );
    }


    /**
     * Get path to application config directory
     * 
     * @return string
     */
    protected function getAppConfigDir()
    {
        return $this->rootDir . 'src/' . $this->appName . '/conf/';
    }


    /**
     * Get path to framework config file
     * 
     * @return string
     */
    protected function getFrameworkConfig()
    {
        return dirname(dirname(__FILE__)) . '/conf/base.php';
    }


    /**
     * Abstraction wrapper for app creation
     *
     * @param  string     $class
     * @param  array|null $args
     * @return object
     */
    protected function makeApp($class, $args)
    {
        $reflect = new \ReflectionClass($class);
        return $reflect->newInstanceArgs($args);
    }


    /**
     * Abstraction for checking root directory constant
     * 
     * @return string|null
     */
    protected function getRootConstant()
    {
        return defined('JHFRAME_ROOTDIR') ? JHFRAME_ROOTDIR : null;
    }


    /**
     * Abstraction wrapper for ini_set()
     * 
     * @see http://php.net/manual/en/function.ini-set.php
     * @param  string $key
     * @param  string $value
     * @return void
     */
    protected function setIniValue($key, $value)
    {
        ini_set($key, $value);
    }

    /**
     * Abstraction wrapper for date_default_timezone_set();
     *
     * @see http://php.net/manual/en/function.date-default-timezone-set.php
     * @param  string $timezone
     * @return void
     */
    protected function setTimezone($timezone)
    {
        date_default_timezone_set($timezone);
    }
}
