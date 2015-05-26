<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Service for crypto-safe operations and hash creation and handling
 */
interface ICrypto
{

    /**
     * Hashes a string
     * 
     * @param  string $string Plaintext
     * @return string
     */
    public function hashString($string);


    /**
     * Compares plain string and hash to see if they match
     * 
     * @param  string  $input    Plaintext
     * @param  string  $existing Hash
     * @return boolean
     */
    public function compareHash($input, $existing);


    /**
     * Checks if a hash needs to be rehashed
     * 
     * @param  string  $hash Password to check
     * @return boolean
     */
    public function hashNeedsUpdating($hash);


    /**
     * Creates crypto-safe random string of given length
     * 
     * @param  integer $length String length
     * @return string
     */
    public function createRandomString($length=64);
}
