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
 * Service to provide storage and retrieval of runtime config values as
 * key/value pairs stored within a related group
 */
interface IConfig
{

    /**
     * Empties config storage
     *
     * @return void
     */
    public function Reset();


    /**
     * Get all of $group or the value of $key in $group
     * 
     * @param string $group Group to return or to check for key
     * @param string $key   Optional key to check
     * @return mixed        Array for group, mixed value for key, null if key
     *                      not found
     */
    public function Get($group, $key=null);


    /**
     * Set $group or $key within $group to $value
     * 
     * @param mixed  $value Array if setting group, mixed if key
     * @param string $group Group to set or to which key belonds
     * @param string $key   Optional key to set
     * @return void
     */
    public function Set($value, $group, $key=null);


    /**
     * Merge $values into $group, overriding values for existing keys
     * 
     * @param array  $values Key/value pairs to merge
     * @param string $group  Group to be merged
     * @return void
     */
    public function Merge(array $values, $group);
}
