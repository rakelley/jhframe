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
 * Interface for Email service
 */
interface IMail
{
    /** Main email account */
    const ACCOUNT_MAIN = 10;
    /** Web admin email account */
    const ACCOUNT_ADMIN = 11;
    /** All administrator accounts */
    const ALL_ADMIN_ACCOUNTS = 12;


    /**
     * Send a new email
     * 
     * @param  mixed  $recipient String email or int corresponding to constant
     * @param  string $title     Email title
     * @param  string $body      Email body
     * @param  mixed  $sender    String email or int corresponding to constant
     * @param  string $headers   Email header
     * @return void
     * @throws \DomainException if unmapped int provided for $recipient or $sender
     */
    public function Send($recipient, $title, $body, $sender=self::ACCOUNT_MAIN,
                         $headers=null);

    /**
     * Returns the email address(es) corresponding to a class or interface
     * constant.  Must minimally support all interface constant values.
     * 
     * @param  int   $const Constant to get const(s) for
     * @return mixed        String email or array of same
     * @throws \DomainException if undefined $const provided
     */
    public function getValueForConstant($const);
}
