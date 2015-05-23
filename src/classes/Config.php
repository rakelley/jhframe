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
 * Default implementation of IConfig
 */
class Config implements \rakelley\jhframe\interfaces\services\IConfig
{
    /**
     * Store for values
     * @var array
     */
    private $config = [];


    /**
     * @see \rakelley\jhframe\interfaces\services\IConfig::Reset
     */
    public function Reset()
    {
        $this->config = [];
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IConfig::Get
     */
    public function Get($group, $key=null)
    {
        if ($key) {
            return (isset($this->config[$group][$key])) ? 
                   $this->config[$group][$key] : null;
        } else {
            return (isset($this->config[$group])) ? 
                   $this->config[$group] : null;
        }
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IConfig::Set
     */
    public function Set($value, $group, $key=null)
    {
        if ($key) {
            $this->config[$group][$key] = $value;
        } else {
            $this->config[$group] = $value;
        }
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IConfig::Merge
     */
    public function Merge(array $values, $group)
    {
        if ($this->Get($group)) {
            $values = array_merge($this->Get($group), $values);
        }
        $this->Set($values, $group);
    }
}
