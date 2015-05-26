<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\view;

/**
 * A view which provides metadata for use in a template
 */
interface IHasMetaData extends IView
{

    /**
     * Public method for view to create or fetch metadata and return it in the
     * form of an associative array
     * 
     * @return array
     */
    public function fetchMetaData();
}
