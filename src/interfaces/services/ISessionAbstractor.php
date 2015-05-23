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
 * Interface for service which abstracts creation and deletion of sessions
 * using PHP built-ins.
 */
interface ISessionAbstractor
{

    /**
     * Destroys current session
     * 
     * @return void
     */
    public function closeSession();

    /**
     * Get current session id
     * 
     * @return string
     */
    public function getId();

    /**
     * Creates new session
     * 
     * @return void
     */
    public function newSession();

    /**
     * Starts session and sets cookie appropriately
     * 
     * @return void
     */
    public function startSession();
}
