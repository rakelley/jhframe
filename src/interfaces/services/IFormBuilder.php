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
 * Interface for service which handles standard form construction for form views
 *
 * For implementation notes on $rules arrays:
 * @see \rakelley\jhframe\interfaces\view\IFormView::getFields
 */
interface IFormBuilder
{

    /**
     * Combines array of HTML attributes into single string for use with any tag
     * 
     * @param  array  $attributes Attribute names and values
     * @return string
     */
    public function combineAttributes(array $attributes);


    /**
     * Generates markup for <input type="checkbox"> field
     * 
     * @param  array  $rules Rules used to construct field
     * @param  null   $data  Currently unused by method
     * @return string
     */
    public function constructCheckbox(array $rules, $data=null);


    /**
     * Generates markup for <input> field
     * 
     * @param  array       $rules Rules used to construct field
     * @param  string|null $data  Optional input value
     * @return string
     */
    public function constructInput(array $rules, $data=null);


    /**
     * Generates <label> markup with support for fields which need to be wrapped
     * by their label rather than have it preceeding.
     * 
     * @param  array  $rules Rules used to construct label
     * @param  string $input Optional input to wrap with label
     * @return string
     */
    public function constructLabel(array $rules, $input='');


    /**
     * Generates markup for <input type="password"> field
     * Appends password strength meter if matching validation class is present
     * 
     * @param  array       $rules Rules used to construct field
     * @param  string|null $data  Optional input value
     * @return string
     */
    public function constructPassword(array $rules, $data=null);


    /**
     * Generaters markup for <select> field
     * 
     * @param  array       $rules    Rules used to construct field
     * @param  array|null  $data     Optional Array of additional options
     * @param  string|null $selected Optional value for option to be marked as
     *                               selected
     * @return string
     */
    public function constructSelect(array $rules, $data=null, $selected=null);


    /**
     * Generates standard form status block
     * 
     * @param  string|null $message Optional message to display on success
     * @return string
     */
    public function constructStatusBlock($message=null);


    /**
     * Generates markup for <textarea> field
     * 
     * @param  array  $rules Rules used to construct field
     * @param  string $data
     * @return string
     */
    public function constructTextarea(array $rules, $data=null);


    /**
     * Generates standard markup for form's title.
     * Can accept either a single string title or an array with 'title' and
     * 'sub' keys containing string title and sub-title.
     *
     * @param  array|string $title
     * @return string
     */
    public function constructTitle($title);
}
