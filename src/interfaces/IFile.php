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
 * Interface for resource abstracting basic file interaction
 */
interface IFile extends IReproducible
{

    /**
     * Set path to file this instance is reponsible for
     * 
     * @param  string $path
     * @return object       $this
     */
    public function setPath($path);


    /**
     * Get file contents
     * 
     * @return string
     * @throws \DomainException if file does not exist
     */
    public function getContent();


    /**
     * Set file contents
     *
     * @param  string $content
     * @return string
     */
    public function setContent($content);


    /**
     * Append to end of file
     * 
     * @param  string $content
     * @return void
     */
    public function Append($content);


    /**
     * Delete file.  Should not throw exception if file doesn't exist.
     * 
     * @return void
     */
    public function Delete();


    /**
     * Whether file exists
     *
     * @return boolean
     */
    public function Exists();


    /**
     * Get file age
     * 
     * @return int Unix timestamp
     * @throws \DomainException if file does not exist
     */
    public function getAge();


    /**
     * Get file media type
     * 
     * @return string
     * @throws \DomainException if file does not exist
     */
    public function getMedia();


    /**
     * Get file size
     * 
     * @return int
     * @throws \DomainException if file does not exist
     */
    public function getSize();
}
