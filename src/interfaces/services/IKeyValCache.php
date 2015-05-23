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
 * Interface for classes which implement some form of key/value cache.
 * Cache should understand keys to be app-specific and provide options for
 * time-based expiry and manual full or partial clears.
 */
interface IKeyValCache
{

    /**
     * Get Value for Key, if it's stored.
     * 
     * @param  string $key
     * @return mixed       boolean false on cache miss, otherwise stored value
     */
    public function Read($key);

    /**
     * Set Value for Key
     * 
     * @param  mixed  $value value to store
     * @param  string $key   key to store value under
     * @return void
     */
    public function Write($value, $key);

    /**
     * Purge cache with an optional filter for partial purge.
     *
     * @param  string|array $filter string to match against or array of same
     * @return void
     */
    public function Purge($filter=null);
}
