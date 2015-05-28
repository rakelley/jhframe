<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes\resources;

/**
 * File Resource, Default implementation for IFile
 */
class File implements \rakelley\jhframe\interfaces\IFile
{
    use \rakelley\jhframe\traits\GetsMimeType,
        \rakelley\jhframe\traits\Reproducible,
        \rakelley\jhframe\traits\ServiceLocatorAware;

    /**
     * Store for defined file path
     * @var string
     */
    protected $path;


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFile::setPath()
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFile::getContent()
     */
    public function getContent()
    {
        $this->mustExist();

        return file_get_contents($this->path);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFile::setContent()
     */
    public function setContent($content)
    {
        file_put_contents($this->path, $content);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFile::Append()
     */
    public function Append($content)
    {
        file_put_contents($this->path, $content, FILE_APPEND);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFile::Delete()
     */
    public function Delete()
    {
        if ($this->Exists()) {
            unlink($this->path);
        }
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFile::Exists()
     */
    public function Exists()
    {
        return file_exists($this->path);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFile::getAge()
     */
    public function getAge()
    {
        $this->mustExist();

        return filemtime($this->path);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFile::getMedia()
     */
    public function getMedia()
    {
        $this->mustExist();

        return $this->getMimeType($this->path);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFile::getSize()
     */
    public function getSize()
    {
        $this->mustExist();

        return filesize($this->path);
    }


    /**
     * Internal method for asserting file exists before otherwise potentially
     * unsafe method is executed.
     * 
     * @return void
     */
    protected function mustExist()
    {
        if (!$this->Exists()) {
            throw new \DomainException(
                'File With Path "' . $this->path . '" Does Not Exist',
                500
            );
        }
    }
}
