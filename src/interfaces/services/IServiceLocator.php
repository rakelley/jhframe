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
 * Serves as dependency resolver/injector and object container
 */
interface IServiceLocator
{

    /**
     * Provide a fresh instance of an object or qualified class name.
     * Useful as an alternative to clone() when you don't want to copy internal
     * state and target has dependencies in constructor.
     * 
     * @param  object|string $existing Class object or class name to create
     * @return object                  New instance of class
     * @throws \RuntimeException If unable to find an appropriate class
     */
    public function getNew($existing);


    /**
     * Returns instance of class from object container, creating it first if
     * necessary.  All class constructor and trait dependencies should be
     * injected.
     * 
     * @param  string $key Classname or key to obtain proper class isntance for
     * @return object
     * @throws \RuntimeException If unable to find an appropriate class
     */
    public function Make($key);


    /**
     * Add new or override existing key/values in class resolution table
     * 
     * @param  array $overrides Associative array to merge into existing table
     * @return void
     */
    public function Override(array $overrides);


    /**
     * Sets state to empty container and class resolution table
     * 
     * @return void
     */
    public function Reset();


    /**
     * Attempts to returns qualified class name corresponding to key from
     * class resolution table
     * 
     * @param  string $key    Dependency to resolve proper class for
     * @return string|boolean Resolved class name or boolean false on failure
     */
    public function Resolve($key);


    /**
     * Stores class objects and associates key with them
     * 
     * @param  array $objects Keys are class or interface key to associate with
     *                        class instance, value is class instance
     * @return void
     */
    public function Store(array $objects);
}
