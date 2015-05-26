<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces;

/**
 * Interface for classes which handle CRUDing a single related group of files
 */
interface IFileHandler
{

    /**
     * Validate file
     * 
     * @param  mixed $file Expected type varies by class, may be array for
     *                     uploaded file or string path to file
     * @return boolean
     */
    public function Validate($file);


    /**
     * Delete file corresponding to $key
     * 
     * @param  string $key Key used to match file
     * @return void
     */
    public function Delete($key);


    /**
     * Find file at path corresponding to $key
     * 
     * @param  string $key Key used to match file
     * @return string      Path to file, or null
     */
    public function Read($key);


    /**
     * Write $file to path corresponding to $key
     * 
     * @param  string $key  Key used to match file
     * @param  mixed  $file Expected type varies by class, may be array for
     *                      uploaded file or string path to file or string uri
     *                      to remote file
     * @return mixed        Default void, may return boolean if write success is
     *                      conditional
     */
    public function Write($key, $file);


    /**
     * Standard method for turning a handled absolute file path into a relative
     * path suitable for using as an URL
     * 
     * @param  string $path Usually gotten from ::Read
     * @return string
     */
    public function makeRelative($path);
}
