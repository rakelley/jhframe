<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Class for fetching user input with standard sanitize and validate options
 *
 * Note on Rules: When rules is an array the following rules must be supported
 * as keys at a minimum
 *    mixed   'filters'      Second argument for IFilter::Filter
 *    mixed   'defaultvalue' Value to use if user input not supplied
 *    string  'equalto'      When getting a list, input with rule equalto must be
 *                           equal to input for key contained in equalto
 *    int     'minlength'    Input must be at least this many characters
 *    int     'maxlength'    Input must not exceed this many characters
 */
interface IInput
{

    /**
     * Fetch an array from user input via http method $method and matching keys
     * of $list using values of $list as rules.  Input can be flagged as optional.
     * 
     * In addition to the normal explicit rules array, a string value of "default"
     * must be supported.  If provided, the defaultRules table should be checked for
     * a matching ruleset to apply.
     * 
     * @param  array   $list     keys to check for, values are rules to
     *                           validate/sanitize data (see rules note)
     * @param  string  $method   HTTP method table to check
     * @param  boolean $optional flag input as optional
     * @return array
     * @throws \RuntimeException if default rules requested and not found
     * @throws \DomainException  if $method is unsupported
     */
    public function getList(array $list, $method, $optional=false);


    /**
     * Fetch all values whose key matches pattern
     * 
     * @param  string $pattern Regex pattern to match against
     * @param  string $method  HTTP method table to check
     * @param  array  $rules   Optional filter rules to apply to matched values
     *                         (see rules notes)
     * @return array
     * @throws \DomainException  if $method is unsupported
     */
    public function searchKeys($pattern, $method, array $rules=null);


    /**
     * Fetch all values that match pattern
     * 
     * @param  string $pattern Regex pattern to match against
     * @param  string $method  HTTP method table to check
     * @param  array  $rules   Optional filter rules to apply to matched values
     *                         (see rules notes)
     * @return array
     * @throws \DomainException  if $method is unsupported
     */
    public function searchValues($pattern, $method, array $rules=null);
}
