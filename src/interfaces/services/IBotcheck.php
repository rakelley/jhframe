<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Service for FormViews and FormActions which are public facing and need to
 * include and validate a form of spam reduction
 */
interface IBotcheck
{

    /**
     * Returns HTML block containing appropriate form fields
     * 
     * @return string
     */
    public function getField();


    /**
     * Performs validation of user input against expecations for human users,
     * raises exception on failure
     * 
     * @return void
     * @throws \rakelley\jhframe\classes\InputException
     */
    public function validateField();
}
