<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Default base implementation of IFileHandler
 * 
 * @abstract
 */
abstract class FileHandler implements \rakelley\jhframe\interfaces\IFileHandler
{
    use \rakelley\jhframe\traits\ConfigAware;

    /**
     * Absolute path to base directory class operates in
     * @var string
     */
    protected $directory;
    /**
     * IFileSystemAbstractor instance
     * @var object
     */
    protected $fileSystem;
    /**
     * Maximum file size in bytes that class accepts
     * @var int
     */
    protected $maxFileSize = 0;
    /**
     * Relative path to class directory from application public dir
     * @var string
     */
    protected $relativePath = '';
    /**
     * List of string mime types class accepts
     * @var array
     */
    protected $validTypes = [];


    function __construct(
        \rakelley\jhframe\interfaces\services\IFileSystemAbstractor $fileSystem
    ) {
        $public = $this->getConfig()->Get('ENV', 'public_dir');
        $this->directory = $public . $this->relativePath;

        $this->fileSystem = $fileSystem;
    }


    /**
     * @see \rakelley\jhframe\interfaces\IFileHandler::Validate
     * @abstract
     */
    abstract public function Validate($file);


    /**
     * @see \rakelley\jhframe\interfaces\IFileHandler::Delete
     */
    public function Delete($key)
    {
        $file = $this->Read($key);

        if ($file) {
            $this->fileSystem->Delete($file);
        }
    }


    /**
     * @see \rakelley\jhframe\interfaces\IFileHandler::Read
     */
    public function Read($key)
    {
        if (strpos($key, '.') !== false) {
            $path = $this->directory . $key;
            return ($this->fileSystem->Exists($path)) ? $path : null;
        } else {
            $pattern = $this->directory . $key . ".*";
            $files = $this->fileSystem->Glob($pattern);

            return ($files) ? $files[0] : null;
        }
    }


    /**
     * @see \rakelley\jhframe\interfaces\IFileHandler::Write
     * @abstract
     */
    abstract public function Write($key, $file);


    /**
     * @see \rakelley\jhframe\interfaces\IFileHandler::makeRelative
     */
    public function makeRelative($path)
    {
        return str_replace($this->directory, $this->relativePath, $path);
    }
}
