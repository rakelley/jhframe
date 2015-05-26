<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\view;

/**
 * A View which requires fetching data from one or more sources before content
 * generation can occur.
 */
interface IRequiresData extends IView
{

    /**
     * Handle for ViewController to execute data-fetch
     * 
     * @return void
     */
    public function fetchData();
}
