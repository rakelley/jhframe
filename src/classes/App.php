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

/**
 * Completes bootstrapping of web application.
 * 
 * Provides method for executing main loop and servicing current HTTP request.
 *
 * Provides global access to ServiceLocator and Config object instances for when
 * class constructor dependency is inelegant.
 */
class App
{
    use \rakelley\jhframe\traits\GetsServerProperty;

    /**
     * Config service instance
     * @var object
     */
    public $config;
    /**
     * ServiceLocator service instance
     * @var object
     */
    public $locator;
    /**
     * Instance of self for static access
     * @var object
     */
    protected static $instance;
    /**
     * Interfaces for dependent services, resolved via standard DI pattern
     * @var array
     */
    protected $services = [
        'globalhandler' =>
            'rakelley\jhframe\interfaces\services\IGlobalExceptionHandler',
        'io'            =>
            'rakelley\jhframe\interfaces\services\IIo',
        'router'        =>
            'rakelley\jhframe\interfaces\services\IRouter',
        'session'       =>
            'rakelley\jhframe\interfaces\services\ISessionAbstractor',
    ];
    /**
     * Config service interface
     */
    const INTERFACE_CONFIG = 'rakelley\jhframe\interfaces\services\IConfig';
    /**
     * ServiceLocator service interface
     */
    const INTERFACE_LOCATOR = 'rakelley\jhframe\interfaces\services\IServiceLocator';


    /**
     * Default Config and ServiceLocator classes are hard-coded, as these
     * services are needed to set up standard DI pattern
     * 
     * @param  string $configClass  Class implementing $this::INTERFACE_CONFIG
     * @param  string $locatorClass Class implementing $this::INTERFACE_LOCATOR
     * @return void
     */
    function __construct($configClass=null, $locatorClass=null)
    {
        if (!$configClass) {
            $configClass = 'rakelley\jhframe\classes\Config';
        }
        if (!$locatorClass) {
            $locatorClass = 'rakelley\jhframe\classes\ServiceLocator';
        }
        $this->config = new $configClass;
        $this->locator = new $locatorClass;

        $overrides = [
            $this::INTERFACE_CONFIG => $this->config,
            $this::INTERFACE_LOCATOR => $this->locator,
        ];
        $this->locator->Store($overrides);
        $this->registerAlias();
        static::$instance = $this;
    }


    /**
     * Public static getter for Config service
     * 
     * @return object
     */
    public static function getConfig()
    {
        return static::$instance->config;
    }


    /**
     * Public static getter for ServiceLocator service
     * 
     * @return object
     */
    public static function getLocator()
    {
        return static::$instance->locator;
    }


    /**
     * Loads the class injection list stored in Config into ServiceLocator.
     * Should be called during bootstrap after all config files are loaded.
     *
     * @return void
     */
    public function setClassListFromConfig()
    {
        $this->locator->Override($this->config->Get('CLASSES'));
    }


    /**
     * Registers the custom global exception handler.
     * Should be called during boostrap after class injection is complete.
     * 
     * @return void
     */
    public function registerExceptionHandler()
    {
        try {
            $global = $this->locator->Make($this->services['globalhandler']);
            $global->registerSelf();
        } catch (\Exception $e) {
            $msg = 'Could Not Register Global Exception Handler: ' .
                   $e->getMessage();
            $this->locator->Make($this->services['io'])
                          ->toExit($msg);
        }
    }


    /**
     * Initiate main app loop and service the current HTTP request.
     * Should be called after app bootstrap and setup is complete.
     * 
     * @return void
     */
    public function serveRequest()
    {
        if ($this->config->Get('APP', 'force_https')) {
            $this->redirectProtocol();
        } else {
            $this->passToRouter();
        }
    }


    /**
     * Forces HTTPS redirect if app has not been loaded locally and is not using
     * HTTPS
     * 
     * @return void
     */
    protected function redirectProtocol()
    {
        $host = $this->getServerProp('HTTP_HOST');
        $uri = $this->getServerProp('REQUEST_URI');

        if ($host && $uri && $this->getServerProp('HTTPS') !== 'on') {
            $header = 'Location: https://' . $host . $uri;
            $this->locator->Make($this->services['io'])
                          ->Header($header)
                          ->toExit();
        } else {
            $this->passToRouter();
        }
    }


    /**
     * Internal method for creating router and servicing current request
     * 
     * @return void
     */
    protected function passToRouter()
    {
        $this->locator->Make($this->services['session'])->startSession();

        $this->locator->Make($this->services['router'])
                      ->serveRequest($this->getServerProp('REQUEST_URI'),
                                     $this->getServerProp('REQUEST_METHOD'));
    }


    /**
     * Creates an alias in the global namespace for this class
     * 
     * @return void
     */
    protected function registerAlias()
    {
        if (!class_exists('\App')) {
            class_alias(get_class($this), '\App');
        }
    }
}
