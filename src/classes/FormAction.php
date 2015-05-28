<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Action associated with a FormView.  Validates user input and creates error
 * or proceeds with data transaction or service call.
 */
abstract class FormAction extends Action implements
    \rakelley\jhframe\interfaces\action\IRequiresValidation
{
    /**
     * Instance of IFormValidator
     * @var object
     */
    protected $formValidator;
    /**
     * Fetched and sanitized user input
     * @var array
     */
    protected $input;
    /**
     * Instance of IFormView
     * @var object
     */
    protected $view;


    /**
     * @param \rakelley\jhframe\interfaces\services\IFormValidator $validator
     * @param \rakelley\jhframe\interfaces\view\IFormView          $view
     */
    function __construct(
        \rakelley\jhframe\interfaces\services\IFormValidator $validator,
        \rakelley\jhframe\interfaces\view\IFormView $view
    ) {
        $this->formValidator = $validator;
        $this->view = $view;
    }


    /**
     * Default implementation of Validate
     * 
     * @see \rakelley\jhframe\interfaces\action\IRequiresValidation::Validate()
     */
    public function Validate()
    {
        $this->input = $this->formValidator->Validate($this->view);

        $this->validateInput();

        return true;
    }


    /**
     * Hook for class-specific validation steps beyond basic form validation.
     * Should throw exception on failure.
     */
    protected function validateInput()
    {
        return;
    }
}
