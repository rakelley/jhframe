<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Interface for service which handles validating form inputs
 */
interface IFormValidator
{

    /**
     * Sanitizes and validates user input according to fields and method defined
     * by IFormView.  Should allow any raised exceptions to bubble.
     *
     * For implementation information:
     * @see \rakelley\jhframe\interfaces\view\IFormView::getFields
     *
     * @param  object $view Object to validate input for
     * @return array        Sanitized user input
     */
    public function Validate(\rakelley\jhframe\interfaces\view\IFormView $view);
}
