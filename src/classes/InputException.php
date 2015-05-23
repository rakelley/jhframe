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
 * Custom Exception type representing missing or invalid user input
 */
class InputException extends \RuntimeException
{
    function __construct($message, $code=400, \Exception $previous=null)
    {
        parent::__construct($message, $code, $previous);
    }
}
