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
 * Trait for classes which need to create thumbnail versions of images
 */
trait CreatesImageThumbnails
{
    protected $thumbnailLibrary = '\rakelley\jhframe\classes\ThumbnailCreator';


    /**
     * ServiceLocator dependency, can be resolved by using
     * \rakelley\jhframe\traits\ServiceLocatorAware
     * @abstract
     */
    abstract protected function getLocator();


    /**
     * Create thumbnail of image using library
     * 
     * @param  string     $originalPath Path to original file
     * @param  array|null $args         Optional args to pass to library
     * @return void
     */
    protected function createThumbnail($originalPath, array $args=null)
    {
        $thumbPath = $this->getThumbPath($originalPath);

        $this->getLocator()->Make($this->thumbnailLibrary)
                           ->createThumbnail($originalPath, $thumbPath, $args);
    }


    /**
     * Convert path to original file into path to thumbnail file
     * 
     * @param  string $originalPath
     * @return string
     */
    abstract protected function getThumbPath($originalPath);
}
