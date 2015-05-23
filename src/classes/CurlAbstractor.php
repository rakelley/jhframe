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
 * Simple wrapper class for curl requests
 */
class CurlAbstractor
{
    /**
     * Current curl handler
     * @var object
     */
    protected $handler;


    /**
     * Initiate new request handler, returns self for chaining
     * 
     * @param  string $uri
     * @return object      $this
     */
    public function newRequest($uri)
    {
        $this->handler = curl_init($uri);

        return $this;
    }


    /**
     * Close handler for current request
     *
     * @return void
     */
    public function Close()
    {
        curl_close($this->handler);
    }


    /**
     * Execute current request
     *
     * @return mixed Void or string if return has been set true
     */
    public function Execute()
    {
        return curl_exec($this->handler);
    }


    /**
     * Get info on current request
     * 
     * @param  string $key
     * @return mixed
     */
    public function getInfo($key)
    {
        return curl_getinfo($this->handler, $key);
    }


    /**
     * Set an option for the current request, returns self for chaining
     * 
     * @param  string $key
     * @param  mixed  $value
     * @return object        $this
     */
    public function setOption($key, $value)
    {
        curl_setopt($this->handler, $key, $value);

        return $this;
    }


    /**
     * Whether to return file contents on execute, returns self for chaining
     * 
     * @param  boolean $value
     * @return object         $this
     */
    public function setReturn($value=true)
    {
        return $this->setOption(CURLOPT_RETURNTRANSFER, $value);
    }
}
