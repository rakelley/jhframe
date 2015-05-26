<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\view;

/**
 * View type which contains a set of fields to use in constructing and
 * validating a HTML form
 */
interface IFormView extends IView
{

    /**
     * Public getter for fields
     * 
     * Field Documentation:
     * Fields is a multi-level array describing fields of the form.  Top level
     * keys are internal names for fields, values are attributes and properties
     * of the field.
     *
     * Optional field members:
     *     string 'label'    Label text for field
     *     bool   'required' If true field is required
     *     array  'attr'     HTML attributes to add to field's tag as key="value"
     *     mixed  'sanitize' string or array describing rules to use to sanitize
     *                       field value
     *
     * Additionally one of 'type' or 'method' must be present
     *     string 'type'     HTML type of field
     *     string 'method'   Custom class method to call to construct field
     *
     * @example
     * $fields = [
     *     'foo' => [
     *         'label' => 'foo field',
     *         'type' => 'textarea',
     *         'required' => true,
     *     ],
     *     'bar' => [
     *         'label' => 'bar field',
     *         'type' => 'password',
     *         'attr' => [
     *             'name' => 'bar',
     *             'class' => 'barField',
     *             'placeholder' => 'Please Enter Bar',
     *         ],
     *     ],
     *     'bat' => [
     *         'method' => 'batMethod',
     *     ],
     * ];
     *
     * Fields are consumed as rule sets by
     * \rakelley\jhframe\interfaces\services\IFormBuilder
     * \rakelley\jhframe\interfaces\services\IFormValidator
     * and any implementation changes to getFields output will break classes
     * implementing those interfaces
     * 
     * @return array
     */
    public function getFields();


    /**
     * Public getter for form's HTTP method
     * 
     * @return string
     */
    public function getMethod();
}
