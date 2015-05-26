<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits\view;

/**
 * Default implementation for \rakelley\jhframe\interfaces\view\IHasSubViews
 */
trait MakesSubViews
{
    protected $controllerInterface =
        '\rakelley\jhframe\interfaces\services\IViewController';
    protected $subViews;


    /**
     * Returns namespace of using class, can be resolved with
     * \rakelley\jhframe\traits\GetCalledNamespace
     * @abstract
     */
    abstract protected function getCalledNamespace();


    /**
     * ServiceLocator dependency, can be resolved by using
     * \rakelley\jhframe\traits\ServiceLocatorAware
     * @abstract
     */
    abstract protected function getLocator();


    /**
     * Internal method of providing list of subviews to generate.
     * Keys will be carried over to the subViews property
     * Values should be fully qualified class names or simple names if namespace
     * is the same as the class using this trait
     * 
     * @return array
     */
    abstract protected function getSubViewList();


    /**
     * @see \rakelley\jhframe\interfaces\view\IHasSubViews::makeSubViews
     */
    public function makeSubViews()
    {
        $viewController = $this->getLocator()->Make($this->controllerInterface);
        $list = $this->getSubViewList();
        $subViews = [];
        $parameters = (isset($this->parameters)) ? $this->parameters : null;

        array_walk(
            $list,
            function($view, $key) use ($parameters, $viewController, &$subViews)
            {
                if (strpos($view, '\\') === false) {
                    $view = $this->getCalledNamespace() . '\\' . $view;
                }
                $subViews[$key] = $viewController->createView($view,
                                                              $parameters)
                                                 ->getContent();
            }
        );

        $this->subViews = $subViews;
    }
}
