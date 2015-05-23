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

namespace rakelley\jhframe\interfaces;

/**
 * Templates are specialized views handling common site headers/footers/etc,
 * turning content from route views into full html pages.
 */
interface ITemplate
{

    /**
     * Builds template and inserts route content into it
     * 
     * @param  string $mainContent Route content to be composited
     * @param  array  $metaData    Route Metadata to use in composition
     * @return string              Composited page ready to render
     */
    public function makeComposite($mainContent, array $metaData=null);
}
