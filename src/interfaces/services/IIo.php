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
     * @see http://php.net/manual/en/function.header.php
     * @param  string $header Header to send
     * @return object         Return $this for chaining
     */
    public function Header($header);


    /**
     * Wrapper for http_response_code
     *
     * @see http://php.net/manual/en/function.http-response-code.php
     * @param  int    $code HTTP code to send
     * @return object       Return $this for chaining
     */
    public function httpCode($code);


    /**
     * Wrapper for echo
     *
     * @see http://php.net/manual/en/function.echo.php
     * @param  string $content
     * @return void
     */
    public function toEcho($content);


    /**
     * Wrapper for error_log
     *
     * @see http://php.net/manual/en/function.error-log.php
     * @param  string      $error
     * @param  int         $type
     * @param  string|null $destination
     * @param  string|null $headers
     * @return void
     */
    public function toErrorLog($error, $type=0, $destination=null,
                               $headers=null);


    /**
     * Wrapper for exit
     *
     * @see http://php.net/manual/en/function.exit.php
     * @param  string|null $message
     * @return void
     */
    public function toExit($message=null);


    /**
     * Wrapper for mail
     *
     * @see http://php.net/manual/en/function.mail.php
     * @param  string      $to
     * @param  string      $subject
     * @param  string      $message
     * @param  string|null $headers
     * @param  string|null $parameters
     * @return bool
     */
    public function toMail($to, $subject, $message, $headers=null,
                           $parameters=null);
}
