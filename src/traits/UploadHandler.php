<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\traits;

use \rakelley\jhframe\classes\InputException;

/**
 * Common implementation for FileHandlers that deal with user-uploaded files
 */
trait UploadHandler
{

    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFileHandler::Validate()
     */
    public function Validate($file)
    {
        if ($file['error'] > 0 ||
            !in_array($this->getMimeType($file['tmp_name']), $this->validTypes)
        ) {
            throw new InputException('File is Not a Valid Type');
        }

        if ($file['size'] > $this->maxFileSize) {
            $max = $this->maxFileSize / 1000;
            throw new InputException(
                'File is Too Large, Must Be Less Than ' . $max . 'kB'
            );
        }

        return true;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFileHandler::Delete()
     */
    abstract public function Delete($key);


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IFileHandler::Write()
     */
    public function Write($key, $file)
    {
        $this->Delete($key);

        $path = $this->directory . $key;
        $ext = $this->getExtension($file);
        if (!strpos($path, $ext)) {
            $path .= $ext;
        }
        $this->fileSystem->writeUploaded($file['tmp_name'], $path);
    }


    /**
     * Derives file extension from mime type
     * 
     * @param  array  $file
     * @return string
     */
    protected function getExtension(array $file)
    {
        $mime = $this->getMimeType($file['tmp_name']);

        return '.' . substr(strrchr($mime, "/"), 1);
    }


    /**
     * Signature for method to get mime type for file
     * 
     * Can be implemented with \rakelley\jhframe\traits\GetsMimeType
     * @abstract
     */
    abstract protected function getMimeType($file);
}
