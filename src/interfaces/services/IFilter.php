<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Library class to sanitize and validate strings and numeric types in various
 * regularly-used ways.
 *
 * All methods should return null on invalid input and avoid throwing exceptions
 * due to bad input.
 */
interface IFilter
{

    /**
     * Public method for filtering string by one or more class methods
     * or global functions.
     *
     * @param  string       $input   string to be filtered
     * @param  string|array $filters method(s) to be used
     * @return string                filtered string
     */
    public function Filter($input, $filters);


    /**
     * Turns delimited string into list, filters each list member, and merges
     * back into delimited string
     * 
     * @param  string $input string to be filtered
     * @param  array  $args  arguments to employ in filtering
     *     string       'separator' delimiter to use in splitting string into list
     *     string|array 'filters'   filter second argument, @see $this->Filter
     * @return string        
     */
    public function asList($input, $args);


    /**
     * Validates and formats string as datetime
     * 
     * @param  string $input  date, time, or date+time string
     * @param  string $format datetime format for output
     * @return string
     */
    public function Date($input, $format='Y-m-d H:i:s');


    /**
     * Sanitizies and validates string as email address
     * 
     * @param  string $input string to be filtered
     * @return string        filtered string
     */
    public function Email($input);


    /**
     * Validates input as float
     * 
     * @param  mixed $input
     * @return float
     */
    public function Float($input);


    /**
     * Filter strings which can contain special characters but need to be
     * protected from XSS
     * 
     * @param  string $input string to be filtered
     * @return string        filtered string
     */
    public function encodeHtml($input);

    /**
     * Reverse of encodeHTML
     * 
     * @param  string $input string to decode
     * @return string        decoded string
     */
    public function decodeHtml($input);


    /**
     * Validates input as int
     * 
     * @param  mixed $input
     * @return int
     */
    public function Int($input);


    /**
     * Converts all spaces in string to underscores and removes multiples
     * 
     * @param  string $input string to filter
     * @return string        filtered string
     */
    public function spaceToUnderscore($input);


    /**
     * Converts all underscores in string to spaces and removes multiples
     * 
     * @param  string $input string to filter
     * @return string        filtered string
     */
    public function underscoreToSpace($input);


    /**
     * Filters strings which may contain basic non-word characters but want all
     * others stripped.  Permits alphanumeric, space, and basic punctuation
     * characters ?!.,- only.
     * 
     * @param  string $input string to be filtered
     * @return string        filtered string
     */
    public function plainText($input);


    /**
     * Accepts strings containing html or html fragments and runs them through
     * stdlib Tidy->repairString.
     * 
     * @param  string $text   string to tidy
     * @param  array  $config tidy args to override defaults
     * @return string         tidied string
     */
    public function tidyText($text, array $config=null);


    /**
     * Makes strings url-safe
     * 
     * @param  string $input String to be filtered
     * @return string
     */
    public function Url($input);


    /**
     * Sanitizes string to only include \w characters (or optional extras)
     * 
     * @param  string $input     string to filter
     * @param  string $permitted optional regex string of additional allowed
     *                           characters
     * @return string            sanitized string
     */
    public function Word($input, $permitted='');
}
