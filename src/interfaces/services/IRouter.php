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
 * Service which translates HTTP requests in the form of a URI and verb into
 * an API call to a controller's method.
 *
 * Must provide a default controller and route (method) to use as fallback
 */
interface IRouter
{

    /**
     * Serve a request by parsing the URI and calling appropriate controller
     * method.
     * Route value should be passed as argument if method accepts one.
     *
     * @param  string $uri  URI for request
     * Expected patterns:
     *     /                      (default controller and route used)
     *     /route                 (default controller used)
     *     /controller/           (default route used)
     *     /controller/route
     *     /controller/route/page
     * 
     * @param  string $type HTTP method for request
     * @return void
     */
    public function serveRequest($uri, $type);
}
