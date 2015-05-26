<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\repository;

/**
 * Interface for repository which provides page metadata
 */
interface IMetaData
{

    /**
     * Get metadata for page, if any, by unique identifier
     * 
     * @param  string     $route Page route
     * @return array|null
     */
    public function getPage($route);
}
