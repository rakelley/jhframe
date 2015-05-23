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

namespace rakelley\jhframe\traits;

/**
 * Trait for classes needing to get a file's media type
 */
trait GetsMimeType
{


    /**
     * Get mime type for file
     * 
     * @param  string $file File path or PHP tmp_name
     * @return string       Mime type
     */
    protected function getMimeType($file)
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        return $finfo->file($file);
    }
}
