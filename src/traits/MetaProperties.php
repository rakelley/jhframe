<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

/**
 * Trait for classes with pseudo-public properties accessed through non-public
 * getters/setters/unsetters
 */
trait MetaProperties
{
    /**
     * Stateful key/value store of properties, state is reset whenever
     * a setter or unsetter is used
     * @var array
     */
    protected $properties = [];


    /**
     * Magic getter, checks stored property array, attempts to use getter method
     * if not stored.
     * 
     * @param  string $key Property name
     * @return mixed
     * @throws \BadMethodCallException if no stored value and no getter method
     */
    public function __get($key)
    {
        $method = 'get' . $key;

        if (isset($this->properties[$key])) {
            return $this->properties[$key];
        } elseif (method_exists($this, $method)) {
            $this->properties[$key] = $this->$method();
            return $this->properties[$key];
        } else {
            $msg = "Call to Get unknown property: " . $key . " in " .
                   get_class($this);
            throw new \BadMethodCallException($msg, 500);
        }
    }


    /**
     * Magic setter, attempts to use setter method and reset state
     * 
     * @param  string $key   Property name
     * @param  mixed  $input Property value
     * @return void
     * @throws \BadMethodCallException if no setter method
     */
    public function __set($key, $input)
    {
        $method = 'set' . $key;

        if (method_exists($this, $method)) {
            $this->$method($input);
            $this->resetProperties();
        } else {
            $msg = "Call to Set unknown property: " . $key . " in " 
                 . get_class($this);
            throw new \BadMethodCallException($msg, 500);
        }
    }


    /**
     * Magic isset, calls getter and returns true if not null
     * 
     * @param  string  $key Property name
     * @return boolean
     */
    public function __isset($key)
    {
        return ($this->__get($key) !== null);
    }


    /**
     * Magic unsetter, attempts to use unset method and reset state
     * @param  string $key Property name
     * @return void
     * @throws \BadMethodCallException if no unsetter method
     */
    public function __unset($key)
    {
        $method = 'unset' . $key;

        if (method_exists($this, $method)) {
            $this->$method();
            $this->resetProperties();
        } else {
            $msg = 'Call to Unset unknown property: ' . $key . ' in ' .
                   get_class($this);
            throw new \BadMethodCallException($msg, 500);
        }
    }


    /**
     * Standardized method for resetting state, should be called by class
     * methods which affect state but are not standard setters/unsetters
     * 
     * @return void
     */
    protected function resetProperties()
    {
        $this->properties = [];
    }
}
