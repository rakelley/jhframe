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
 * Default implementation of ICrypto
 */
class Crypto implements \rakelley\jhframe\interfaces\services\ICrypto
{
    private $hashStrength = \PASSWORD_DEFAULT;


    /**
     * @see \rakelley\jhframe\interfaces\services\ICrypto::hashString
     */
    public function hashString($string)
    {
        return password_hash($string, $this->hashStrength);
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\ICrypto::compareHash
     */
    public function compareHash($input, $existing)
    {
        return password_verify($input, $existing);
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\ICrypto::hashNeedsUpdating
     */
    public function hashNeedsUpdating($hash)
    {
        return password_needs_rehash($hash, $this->hashStrength);
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\ICrypto::createRandomString
     */
    public function createRandomString($length=64)
    {
        return bin2hex(openssl_random_pseudo_bytes($length / 2));
    }
}
