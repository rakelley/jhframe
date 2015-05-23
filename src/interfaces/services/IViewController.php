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

namespace rakelley\jhframe\interfaces\services;

/**
 * Controller class for Views, steps through their public methods and produces a
 * Renderable
 */
interface IViewController
{

    /**
     * Fetches or generates view and returns it as a Renderable
     * 
     * @param  string  $viewName   Qualified class name of view to generate
     * @param  array   $parameters Optional parameters to pass to view
     * @param  boolean $cacheable  If the view is cacheable at the request level
     * @return object              \rakelley\jhframe\interfaces\IRenderable
     */
    public function createView($viewName, array $parameters=null,
                               $cacheable=false);
}
