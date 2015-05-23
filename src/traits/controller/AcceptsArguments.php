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
 * Trait for RouteControllers needing to validate a set of arguments before
 * executing a route
 */
trait AcceptsArguments
{

    /**
     * Attempt to validate and return a set of arguments through the
     * appropriate action
     * 
     * @param  array   $arguments    Set of arguments to retrieve
     *     array  'required'         Array of required arguments and their
     *                               validation rules
     *     array  'accepted'         Array of optional arguments and their
     *                               validation rules
     *     string 'method'           HTTP method
     * @param  boolean $mustValidate Whether to throw exception on validation
     *                               failure
     * @return mixed                 Array on success, null otherwise
     * @throws \UnexpectedValueException if mustValidate and failure
     */
    protected function getArguments(array $arguments, $mustValidate=true)
    {
        $actionClass = 'rakelley\jhframe\classes\ArgumentValidator';

        $result = $this->actionController->executeAction($actionClass,
                                                         $arguments);

        if ($mustValidate && !$result->getSuccess()) {
            throw new \UnexpectedValueException($result->getError(), 400);
        } else {
            return $result->getMessage();
        }
    }
}
