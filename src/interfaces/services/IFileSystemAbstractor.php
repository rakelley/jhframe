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
 * Interface for service abstracting basic filesystem interaction to make
 * writing/testing classes that interact with it simpler.
 */
interface IFileSystemAbstractor
{

    /**
     * Whether file or directory at $path exists
     * 
     * @param  string $path
     * @return boolean
     */
    public function Exists($path);


    /**
     * Create directory at $path
     * 
     * @param  string $path
     * @return void
     */
    public function createDirectory($path);


    /**
     * Create file at $path
     * 
     * @param  string $path
     * @return object       IFile instance for file at $path
     */
    public function createFile($path);


    /**
     * PHP includes file in an object container and returns result
     *
     * @param  string $path       File path
     * @param  array  $parameters Optional parameters for file execution
     * @return mixed
     */
    public function containeredInclude($path, array $parameters=null);


    /**
     * PHP includes file without container, may output to user.
     * ::containeredInclude is preferred
     *
     * @param  string $path       File path
     * @param  array  $parameters Optional parameters for file execution
     * @return mixed
     */
    public function unsafeInclude($path, array $parameters=null);


    /**
     * Get file at $path
     * 
     * @param  string $path
     * @return object       IFile instance for file at $path
     */
    public function getFileWithPath($path);


    /**
     * Retrieve remote file
     * 
     * @param  string $uri
     * @return string
     */
    public function getRemoteFile($uri);


    /**
     * Search for files with $pattern
     * 
     * @param  string $pattern
     * @param  int    $flags   global flags for php glob call
     * @return array
     */
    public function Glob($pattern, $flags=0);


    /**
     * Delete file or directory with recursive option
     * 
     * @param  mixed   $path      String path name, \SplFileInfo instance, or
     *                            array containing either
     * @param  boolean $recursive Whether to recursively remove children if
     *                            $path points to a directory
     * @return void
     * @throws \DomainException If trying to delete a directory with children
     *                          with !$recursive
     */
    public function Delete($path, $recursive=false);


    /**
     * Write uploaded file
     * 
     * @param  string $tmpName     PHP tmp_name for uploaded file
     * @param  string $destination Path to write file to
     * @return void
     */
    public function writeUploaded($tmpName, $destination);
}
