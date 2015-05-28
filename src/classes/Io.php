<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;


/**
 * Default implementation of IIo service, providing abstraction of input/output
 */
class Io implements \rakelley\jhframe\interfaces\services\IIo
{

    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IIo::getInputTable()
     */
    public function getInputTable($type)
    {
        $type = strtolower($type);

        switch ($type) {
            case 'get':
                return $_GET;
                break;

            case 'post':
                return $_POST;
                break;

            case 'files':
                return $_FILES;
                break;

            case 'cookie':
                return $_COOKIE;
                break;

            default:
                throw new \DomainException(
                    'Invalid Global "' . $type . '" Specified',
                    500
                );
                break;
        }
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IIo::Header()
     */
    public function Header($header)
    {
        header($header);

        return $this;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IIo::httpCode()
     */
    public function httpCode($code)
    {
        http_response_code($code);

        return $this;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IIo::toEcho()
     */
    public function toEcho($content)
    {
        echo($content);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IIo::toErrorLog()
     */
    public function toErrorLog($error, $type=0, $destination=null,
                               $headers=null)
    {
        error_log($error, $type, $destination, $headers);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IIo::toExit()
     */
    public function toExit($message=null)
    {
        exit($message);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IIo::toMail()
     */
    public function toMail($to, $subject, $message, $headers=null,
                           $parameters=null)
    {
        return mail($to, $subject, $message, $headers, $parameters);
    }
}
