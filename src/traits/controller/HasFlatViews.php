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

namespace rakelley\jhframe\traits\controller;

/**
 * Trait for RouteControllers which have flat html views
 */
trait HasFlatViews
{

    /**
     * Returns namespace of using class, can be resolved with
     * \rakelley\jhframe\traits\GetCalledNamespace
     * @abstract
     */
    abstract protected function getCalledNamespace();


    /**
     * @see \rakelley\jhframe\classes\RouteController::standardView
     * @abstract
     */
    abstract protected function standardView($name, array $parameters=null,
                                             $cacheable=true);


    /**
     * Default route for RouteControllers to map to for flat views
     * 
     * @param  string $route View name, passed by Router
     * @return void
     */
    public function flatView($route)
    {
        $this->serveFlatView($route);
    }


    /**
     * Implementation of call to FlatView.
     * Namespace arg should only be provided if calling a view in a namespace
     * other than the one belonging to the using RouteController.
     * 
     * @param  string      $view      View name
     * @param  string|null $namespace Optional override for default namespace
     * @return void
     */
    protected function serveFlatView($view, $namespace=null)
    {
        if (!$namespace) {
            $namespace = $this->getCalledNamespace() . '\views';
        }
        $parameters = ['view' => $view, 'namespace' => $namespace];

        $this->standardView('rakelley\jhframe\classes\FlatView', $parameters);
    }
}
