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

namespace rakelley\jhframe\interfaces\view;

/**
 * A view which depends on one or more additional injected views which must have
 * their factory methods called so their content can be used in the parent view
 */
interface IHasSubViews extends IView
{

    /**
     * Public handle for ViewController to execute creation of subviews
     * 
     * @return void
     */
    public function makeSubViews();
}
