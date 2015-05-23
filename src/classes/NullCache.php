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

namespace rakelley\jhframe\classes;

/**
 * Non-functional stub implementation of IKeyValCache, when no cacheing is
 * desired
 */
class NullCache implements \rakelley\jhframe\interfaces\services\IKeyValCache
{

    /**
     * @see \rakelley\jhframe\interfaces\services\IKeyValCache::Read
     */
    public function Read($key)
    {
        return false;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IKeyValCache::Write
     */
    public function Write($value, $key)
    {
        return;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IKeyValCache::Purge
     */
    public function Purge($filter=null)
    {
        return;
    }
}
