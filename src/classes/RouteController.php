<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * RouteControllers define valid HTTP routes via regex and pair those routes
 * with methods that perform actions, return data, or render views.
 *
 * All class methods paired to routes must be public and can optionally
 * accept a single argument from the Router, a string of the route used.
 * 
 * @abstract
 */
abstract class RouteController
{
    use \rakelley\jhframe\traits\GetCalledNamespace;

    /**
     * ActionController instance
     * @var object
     */
    protected $actionController;
    /**
     * All RouteControllers using the default implementation of ::matchRoute
     * must provide a two-level array of valid routes and their matching
     * methods.
     * First level should have keys of http methods.  Second level should have
     * keys of regexs to match against and values of class method names.
     *
     * @example
     * protected $routes = [
     *     'get' => [
     *         '/foo/' => 'showFoo',
     *         '/\w/'  => 'doBar',
     *     ],
     *     'post' => [
     *         '/[\d]/' => 'setId',
     *     ]
     * ];
     * public function showFoo()
     * {
     * }
     * public function doBar()
     * {
     * }
     * public function setId($id)
     * {
     *     //if route is 1234, $id is '1234'
     * }
     * 
     * @var array
     */
    protected $routes = [];
    /**
     * ViewController instance
     * @var object
     */
    protected $viewController;


    /**
     * @param \rakelley\jhframe\interfaces\services\IActionController $actionController
     * @param \rakelley\jhframe\interfaces\services\IViewController   $viewController
     */
    function __construct(
        \rakelley\jhframe\interfaces\services\IActionController $actionController,
        \rakelley\jhframe\interfaces\services\IViewController $viewController
    ) {
        $this->actionController = $actionController;
        $this->viewController = $viewController;
    }


    /**
     * Checks controller's routes table for a method corresponding to a route
     * 
     * @param  string $type  HTTP method
     * @param  string $route Route string to regex match to entries in table
     * @return string        Name of public method that matches the route
     * @throws \UnexpectedValueException if no match found
     */
    public function matchRoute($type, $route)
    {
        if (isset($this->routes[$type])) {
            foreach ($this->routes[$type] as $regex => $method) {
                if (preg_match($regex, $route)) return $method;
            }
        }

        $msg = 'No Route Matching ' . $route . ' Found for HTTP Method ' .
               $type;
        throw new \UnexpectedValueException($msg, 404);
    }


    /**
     * Standardized internal method for rendering a view
     * 
     * @param  string  $name       Qualified class name of view
     * @param  array   $parameters Optional parameters to pass to view
     * @param  boolean $cacheable  If route is cacheable
     * @return void
     */
    protected function standardView($name, array $parameters=null,
                                    $cacheable=true)
    {
        if (strpos($name, '\\') === false) {
            $name = $this->getCalledNamespace() . '\views\\' . $name;
        }
        $this->viewController->createView($name, $parameters, $cacheable)
                             ->Render();
    }


    /**
     * Standardized internal method for executing an action and rendering the
     * result
     * 
     * @param  string $name       Qualified class name of action
     * @param  array  $parameters Optional parameters to pass to action
     * @return void
     */
    protected function standardAction($name, array $parameters=null)
    {
        if (strpos($name, '\\') === false) {
            $name = $this->getCalledNamespace() . '\actions\\' . $name;
        }
        $this->actionController->executeAction($name, $parameters)
                               ->Render();
    }
}
