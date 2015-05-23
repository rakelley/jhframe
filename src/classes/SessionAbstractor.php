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
 * Default implementation of ISessionAbstractor
 */
class SessionAbstractor implements
    \rakelley\jhframe\interfaces\services\ISessionAbstractor
{

    /**
     * @see \rakelley\jhframe\interfaces\services\ISessionAbstractor::getId
     */
    public function getId()
    {
        return session_id();
    }


    /**
     * This implementation only works on the subdomain it's created on, and only
     * over https.
     * 
     * @see \rakelley\jhframe\interfaces\services\ISessionAbstractor::startSession
     */
    public function startSession() {
        if ($this->getId()) {
            return;
        }

        // domain arg must be null to avoid PHP adding the wildcard prefix
        session_set_cookie_params(10800, '/', null, true, true);

        session_start();
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\ISessionAbstractor::newSession
     */
    public function newSession()
    {
        $this->startSession();
        session_regenerate_id();
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\ISessionAbstractor::closeSession
     */
    public function closeSession()
    {
        $this->getId() or session_start();
        setcookie(session_name(), '', time() - 3600, '/');
        session_regenerate_id(true);
        session_unset();
        session_destroy();
        session_write_close();
    }
}
