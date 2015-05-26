<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Service providing simple input/output abstraction
 */
interface IIo
{
    /**
     * Getter for global input arrays
     * Implementation must minimally support 'get', 'post', 'files'
     *
     * @param  string $type Global array to return
     * @return array        Table corresponding to type
     * @throws \DomainException if $type is invalid
     */
    public function getInputTable($type);


    /**
     * Wrapper for header
     * 
     * @param  string $header Header to send
     * @return object         Return self for chaining
     */
    public function Header($header);


    /**
     * Wrapper for http_response_code
     * 
     * @param  int    $code HTTP code to send
     * @return object       Return self for chaining
     */
    public function httpCode($code);


    /**
     * Wrapper for echo
     * 
     * @param  string $content
     * @return void
     */
    public function toEcho($content);


    /**
     * Wrapper for error_log
     * 
     * @param  string      $error       Same as error_log
     * @param  int         $type        Same as error_log
     * @param  string|null $destination Same as error_log
     * @param  string|null $headers     Same as error_log
     * @return void
     */
    public function toErrorLog($error, $type=0, $destination=null,
                               $headers=null);


    /**
     * Wrapper for exit
     * 
     * @param  string|null $message Optional exit message
     * @return void
     */
    public function toExit($message=null);


    /**
     * Wrapper for mail
     * 
     * @param  string $to         Same as mail
     * @param  string $subject    Same as mail
     * @param  string $message    Same as mail
     * @param  string $headers    Same as mail
     * @param  string $parameters Same as mail
     * @return bool
     */
    public function toMail($to, $subject, $message, $headers=null,
                           $parameters=null);
}
