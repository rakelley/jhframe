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

namespace rakelley\jhframe\traits\controller;

/**
 * Trait for RouteControllers which need to authenticate user permission before
 * executing route.
 * Controllers may pass an argument to ::routeAuth or provide a $permission
 * class property for reusability across multiple routes.
 */
trait Authenticated
{
    /**
     * Interface for AuthService
     * @var string
     */
    protected $authServiceInterface =
        '\rakelley\jhframe\interfaces\services\IAuthService';


    /**
     * ServiceLocator dependency, can be resolved by using
     * \rakelley\jhframe\traits\ServiceLocatorAware
     * @abstract
     */
    abstract protected function getLocator();


    /**
     * Internal method for verifying authorization to execute route
     * 
     * @param  string $override permission to check instead of class permission
     * @return void
     * @throws \InvalidArgumentException if no permission provided by controller
     * @throws \RuntimeException if not authorized
     */
    protected function routeAuth($override=null)
    {
        if ($override) {
            $permission = $override;
        } elseif (isset($this->permission)) {
            $permission = $this->permission;
        } else {
            throw new \InvalidArgumentException(
                'Attempt to Check Null Permission',
                500
            );
        }

        $success = $this->getLocator()->Make($this->authServiceInterface)
                                      ->checkPermission($permission);
        if (!$success) {
            throw new \RuntimeException(
                'You Do Not Have Permission To Do This',
                403
            );
        }
    }
}
