<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Default implementation of IRouter
 */
class Router implements \rakelley\jhframe\interfaces\services\IRouter
{
    use \rakelley\jhframe\traits\ConfigAware,
        \rakelley\jhframe\traits\ServiceLocatorAware;

    /**
     * Name of currently loaded app, used to specify path to routes
     * @var string
     */
    protected $appName;
    /**
     * Controller used if none present in URI
     * @var string
     */
    protected $defaultController = 'flat';
    /**
     * Route used if none present in URI
     * @var string
     */
    protected $defaultRoute = 'index';


    function __construct()
    {
        $this->appName = $this->getConfig()->Get('APP', 'name');
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IRouter->serveRequest
     */
    public function serveRequest($uri, $type)
    {
        $type = strtolower($type);
        if ($type === 'head') $type = 'get';

        $parts = $this->parseURI($uri);

        $controller = $this->getLocator()->Make($parts['controller']);
        $method = $controller->matchRoute($type, $parts['route']);

        if ($this->methodTakesArg($controller, $method)) {
            $controller->$method($parts['route']);
        } else {
            $controller->$method();
        }
    }


    /**
     * Render URI string into controller and route information
     *
     * @param  string $uri URI to parse
     * @return array
     *     string 'controller' controller whose methods are to be called
     *     string 'route'      route pattern to use to pick controller method
     * @throws \UnexpectedValueException if unexpected URI pattern
     */
    protected function parseURI($uri)
    {
        $end = (strpos($uri, '?')) ?: strlen($uri);
        $uri = strtolower(substr($uri, 1, $end-1));
        $parts = explode('/', $uri);

        switch (count($parts)) {
            case 1:// '/' or '/foo'
                $result['controller'] = $this->defaultController;
                $result['route'] = ($parts[0]) ?: $this->defaultRoute;
                break;
            
            case 2:// '/foobar/' or '/foobar/baz'
                $result['controller'] = $parts[0];
                $result['route'] = ($parts[1]) ?: $this->defaultRoute;
                break;
            
            case 3:// '/foobar/baz/3'
                $result['controller'] = $parts[0];
                $result['route'] = $parts[1];
                $_GET['page'] = $parts[2];
                $_REQUEST['page'] = $parts[2];
                break;

            default:
                throw new \UnexpectedValueException(
                    'Invalid URI Format: ' . $uri,
                    404
                );
                break;
        }

        $result['controller'] = $this->validateController($result['controller']);

        return $result;
    }


    /**
     * Verifies that controller exists and returns qualified class name
     * 
     * @param  string $controller unqualified controller name to verify
     * @return string             qualified name of controller
     * @throws \UnexpectedValueException if controller doesn't exist
     */
    protected function validateController($controller)
    {
        $qualified = $this->getQualifiedController($controller);

        $class = $this->getLocator()->Resolve($qualified);
        if (!$class) {
            throw new \UnexpectedValueException(
                'Controller ' . $controller . ' Not Found',
                404
            );
        }

        return $class;
    }


    /**
     * Maps a plain controller name to a qualified class name.
     * Should be overridden if your app namespace structure differs.
     * 
     * @param  string $name Controller name
     * @return string
     */
    protected function getQualifiedController($name)
    {
        $name = ucfirst(strtolower($name));
        $app = $this->appName;

        return "$app\\routes\\$name\\$name";
    }


    /**
     * Abstraction for checking if controller method should be provided an
     * argument
     * 
     * @param  object  $controller Controller instance
     * @param  string  $method     Name of method
     * @return boolean             Whether the method expects an arg
     */
    protected function methodTakesArg($controller, $method)
    {
        $reflect = new \ReflectionMethod($controller, $method);
        return !!$reflect->getParameters();
    }
}
