<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

/**
 * Trait for getting one or more random elements from an array
 */
trait PickRandomArrayElements
{

    /**
     * Returns randomized selection of array elements
     * 
     * @param  array  $all   Array to randomly pick from
     * @param  int    $count Maximum number of elements to return
     * @return array         Selection, or null if $all is empty
     */
    protected function pickRandomArrayElements(array $all, $count)
    {
        if (!$all) {
            $result = null;
        } elseif ($count >= count($all)) {
            $result = $all;
            shuffle($result);
        } else {
            //array_rand doesn't return an array for a count of 1
            $keys = (array) array_rand($all, $count);

            $result = array_map(
                function($key) use ($all) {
                    return $all[$key];
                },
                $keys
            );
            shuffle($result);
        }

        return $result;
    }
}
