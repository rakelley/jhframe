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

namespace rakelley\jhframe\traits\view;

/**
 * Trait implementation of replacing placeholders with values, useful for views
 * which need to recycle a subview many times.
 */
trait InterpolatesPlaceholders
{

    /**
     * Replace all placeholders denoted with '%' with values
     * 
     * @param  string $view      Content containing placeholders
     * @param  array  $variables Placeholders and replacement values
     * @return string
     */
    protected function interpolatePlaceholders($view, array $variables)
    {
        $replace = [];
        array_walk(
            $variables,
            function($val, $key) use (&$replace) {
                $replace['%' . $key . '%'] = $val;
            }
        );

        return strtr($view, $replace);
    }
}
