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
 * Interface for service which provides high-level login session control for
 * the current user and can authenticate their permissions and properties
 */
interface IAuthService
{
    /**
     * Permission value when all logged in users are permitted access
     */
    const PERMISSION_ALLUSERS = 'allusers';


    /**
     * Get one or all properties of current user
     * 
     * @param  string $key Optional name of user property to return
     * @return mixed       Array of complete user properties if no key, or
     *                     mixed value for key in user property array, or
     *                     null if no current user
     */
    public function getUser($key=null);


    /**
     * Checks whether current user has a permission.  Should always return true
     * for PERMISSION_ALLUSERS if there is a valid logged in user.
     * 
     * @param  string  $permission Permission to check
     * @return boolean
     */
    public function checkPermission($permission);


    /**
     * Create new session and set current user
     * 
     * @param  string $username username to set as current user
     * @return void
     */
    public function logIn($username);


    /**
     * Destroy current session and unset current user
     * 
     * @return void
     */
    public function logOut();
}
