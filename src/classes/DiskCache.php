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

namespace rakelley\jhframe\classes;

/**
 * Implementation of IKeyValCache which stores key/val pairs as files.
 * The Key become a md5-ed filename and the value become JSONed file content.
 *
 * Class can be extended to use something other than JSON by overwriting the
 * 'extension' const and the encode/decode methods.
 */
class DiskCache implements \rakelley\jhframe\interfaces\services\IKeyValCache
{
    use \rakelley\jhframe\traits\ConfigAware;

    /**
     * Name of currently loaded app
     * @var string
     */
    protected $appName;
    /**
     * Path to cache file directory
     * @var string
     */
    protected $directory;
    /**
     * Extension for cache files
     * @var string
     */
    protected $extension = '.json';
    /**
     * FileSystemAbstractor instance
     * @var object
     */
    protected $fileSystem;
    /**
     * Cache expiration in seconds, set to 0 or null for no expiration
     * @var int
     */
    protected $lifetime;


    function __construct(
        \rakelley\jhframe\interfaces\services\IFileSystemAbstractor $fileSystem
    ) {
        $this->fileSystem = $fileSystem;

        $this->appName = $this->getConfig()->Get('APP', 'name');

        $this->directory = $this->getConfig()->Get('ENV', 'cache_dir');

        $this->lifetime = $this->getConfig()->Get('ENV', 'cache_lifetime');
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IKeyValCache::Read
     */
    public function Read($key)
    {
        $path = $this->getFilePath($key);
        $file = $this->fileSystem->getFileWithPath($path);
        $exists = $file->Exists();

        if ($this->lifetime && $exists &&
            (time() - $file->getAge() > $this->lifetime)
        ) {
            $file->Delete();
        } elseif ($exists) {
            return $this->decodeContent($file->getContent());
        }

        return false;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IKeyValCache::Write
     */
    public function Write($value, $key)
    {
        $path = $this->getFilePath($key);
        $file = $this->fileSystem->getFileWithPath($path);

        $file->setContent($this->encodeContent($value));
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IKeyValCache::Purge
     *
     * Currently the filter is only partially functional as keys are hashed to
     * avoid issues with special characters in file names, only exact keys will
     * be matched.
     * @todo revisit key usage to permit wipes based on partial keys
     */
    public function Purge($filter=null)
    {
        if (is_array($filter)) {
            array_map([$this, 'Purge'], $filter);
            return;
        }

        if ($filter) {
            $filter = $this->filterKey($filter);
        } else {
            $filter = '*';
        }

        $pattern = $this->directory . $filter . $this->extension;
        $files = $this->fileSystem->Glob($pattern);
        if ($files) {
            $this->fileSystem->Delete($files);
        }
    }


    /**
     * Internal method for getting file path associated with key
     * 
     * @param  string $key
     * @return string
     */
    protected function getFilePath($key)
    {
        return $this->directory . $this->filterKey($key) . $this->extension;
    }


    /**
     * Internal method for rendering key into a usable state
     * 
     * @param  string $key key to change
     * @return string      result
     */
    protected function filterKey($key)
    {
        return hash('md5', $this->appName . $key);
    }


    /**
     * Internal method for decoding cache content
     * 
     * @param  string $content Cached string to decode
     * @return mixed           Decoded value
     */
    protected function decodeContent($content)
    {
        return json_decode($content, true);
    }


    /**
     * Internal method for encoding cache content
     * 
     * @param  mixed  $content Value to encode
     * @return string          Encoded string to cache
     */
    protected function encodeContent($content)
    {
        return json_encode($content);
    }
}
