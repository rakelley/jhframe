<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Default implementation of IFileSystemAbstractor
 * @see \rakelley\jhframe\interfaces\services\IFileSystemAbstractor
 */
class FileSystemAbstractor implements
    \rakelley\jhframe\interfaces\services\IFileSystemAbstractor
{
    use \rakelley\jhframe\traits\ServiceLocatorAware;

    /**
     * CurlAbstractor instance
     * @var object
     */
    protected $curl;
    /**
     * File resource instance
     * @var object
     */
    protected $fileResource;


    /**
     * @param \rakelley\jhframe\classes\CurlAbstractor $curl
     * @param \rakelley\jhframe\interfaces\IFile       $fileResource
     */
    function __construct(
        \rakelley\jhframe\classes\CurlAbstractor $curl,
        \rakelley\jhframe\interfaces\IFile $fileResource
    ) {
        $this->curl = $curl;
        $this->fileResource = $fileResource;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFileSystemAbstractor::Exists()
     */
    public function Exists($path)
    {
        return (file_exists($path) || is_dir($path));
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFileSystemAbstractor::createDirectory()
     */
    public function createDirectory($path)
    {
        mkdir($path);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFileSystemAbstractor::createFile()
     */
    public function createFile($path)
    {
        touch($path);
        return $this->getFileWithPath($path);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFileSystemAbstractor::containeredInclude()
     */
    public function containeredInclude($path, array $parameters=null)
    {
        ob_start();
        include($path);
        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFileAbstractor::Include()
     */
    public function unsafeInclude($path, array $parameters=null)
    {
        include($path);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFileSystemAbstractor::getFileWithPath()
     */
    public function getFileWithPath($path)
    {
        return $this->fileResource->getNewInstance()
                                  ->setPath($path);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFileSystemAbstractor::getRemoteFile()
     */
    public function getRemoteFile($uri)
    {
        $file = $this->curl->newRequest($uri)
                           ->setReturn()
                           ->Execute();
        $this->curl->Close();

        return $file;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFileSystemAbstractor::Glob()
     */
    public function Glob($pattern, $flags=0)
    {
        return glob($pattern, $flags);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFileSystemAbstractor::Delete()
     */
    public function Delete($path, $recursive=false)
    {
        if (is_array($path)) {
            if (!$recursive) {
                array_map([$this, 'Delete'], $path);    
            } else {
                $recArgArray = array_fill(0, count($path), true);
                array_map([$this, 'Delete'], $path, $recArgArray);
            }
            return;
        } elseif ($path instanceof \SplFileInfo) {
            $path = $path->getPathname();
        } elseif (!$this->Exists($path)) {
            return;
        }

        if (is_dir($path)) {
            $this->deleteDir($path, $recursive);
        } else {
            unlink($path);
        }
    }

    /**
     * Internal method for directory deletion
     * 
     * @param  string  $rootPath  Path for root directory
     * @param  boolean $recursive Recursion flag
     * @return void
     * @throws \DomainException If directory has children and !$recursive
     */
    protected function deleteDir($rootPath, $recursive)
    {
        $directory = new \RecursiveDirectoryIterator(
            $rootPath,
            \FilesystemIterator::SKIP_DOTS
        );

        if ($directory->hasChildren()) {
            if (!$recursive) {
                throw new \DomainException(
                    'Directory "' . $rootPath . '" Still Has Children',
                    500
                );
            }

            $iterator = new \RecursiveIteratorIterator(
                $directory,
                \RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($iterator as $child) {
                $this->Delete($child);
            }
        }

        rmdir($rootPath);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFileSystemAbstractor::writeUploaded()
     */
    public function writeUploaded($tmpName, $destination)
    {
        move_uploaded_file($tmpName, $destination);
    }
}
