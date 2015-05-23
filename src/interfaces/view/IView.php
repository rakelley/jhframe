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
 * Minimum public interface for presentation class
 */
interface IView
{

    /**
     * Generate content of view in preparation of return
     * 
     * @return void
     */
    public function constructView();


    /**
     * Returns View properties as array
     * 
     * @return array
     *    string 'content' Generated content of view
     *    string 'type'    Optional type of view content
     *    array  'meta'    Optional metadata of view
     * @throws \BadMethodCallException if content has not been generated
     */
    public function returnView();
}
