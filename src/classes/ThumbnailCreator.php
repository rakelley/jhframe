<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Convenience class for creating thumbnails of images using PHP built-ins
 */
class ThumbnailCreator
{
    use \rakelley\jhframe\traits\GetsMimeType;

    /**
     * Default maximum thumbnail height
     * @var integer
     */
    protected $defaultThumbHeight = 258;
    /**
     * Default maximum thumbnail width
     * @var integer
     */
    protected $defaultThumbWidth = 344;


    /**
     * Create a thumbnail image from an existing one
     * 
     * @param  string $originalPath Path to source file
     * @param  string $thumbPath    Target path for thumbnail
     * @param  array  $args         Optional args to use in creation
     *     int 'maxwidth'  Maximum width of thumbnail
     *     int 'maxheight' Maximum height of thumbnail
     * @return void
     */
    public function createThumbnail($originalPath, $thumbPath, array $args=null)
    {
        $max['width'] = (isset($args['maxwidth'])) ? $args['maxwidth'] :
                        $this->defaultThumbWidth;
        $max['height'] = (isset($args['maxheight'])) ? $args['maxheight'] :
                         $this->defaultThumbHeight;

        $original = $this->prepareOriginal($originalPath);

        $thumb = $this->prepareThumb($thumbPath, $original, $max);

        $this->Resize($thumb, $original);

        $this->writeImage($thumb);
    }


    /**
     * Derives properties for source image
     * 
     * @param  string $path Path to source image
     * @return array
     *     string 'path'     Path to image
     *     string 'type'     Image type
     *     int    'width'    Image width
     *     int    'height'   Image height
     *     object 'resource' Image resource
     *     int    'x'        Resource X-origin for thumbnail creation
     *     int    'y'        Resource Y-origin for thumbnail creation
     */
    protected function prepareOriginal($path)
    {
        $oSize = $this->getSize($path);

        $o = ['path' => $path];
        $o['type'] = $this->getType($o['path']);
        $o['width'] = $oSize[0];
        $o['height'] = $oSize[1];
        $o['resource'] = $this->createFrom($o['path'], $o['type']);
        $o['x'] = 0;
        $o['y'] = 0;

        return $o;
    }


    /**
     * Generates properties for thumbnail image
     * 
     * @param  string $path Target path for thumbnail image
     * @param  array  $o    Properties for original image
     * @param  array  $max  Maximum size for thumbnail
     *     int 'width'
     *     int 'height'
     * @return array
     *     string 'path'     Path to image
     *     string 'type'     Image type
     *     int    'width'    Image width
     *     int    'height'   Image height
     *     object 'resource' Image resource
     *     int    'x'        Resource X-origin for thumbnail creation
     *     int    'y'        Resource Y-origin for thumbnail creation
     */
    protected function prepareThumb($path, array $o, array $max)
    {
        $t = ['path' => $path];
        $t['type'] = $o['type'];
        if ($o['width'] > $o['height']) {
            $t['width'] = $max['width'];
            $t['height'] = intval($o['height'] * $t['width'] / $o['width']);
        } else {
            $t['height'] = $max['height'];
            $t['width'] = intval($o['width'] * $t['height'] / $o['height']);
        }
        $t['resource'] = $this->createNew($max['width'], $max['height']);
        $t['x'] = intval(($max['width'] - $t['width']) / 2);
        $t['y'] = intval(($max['height'] - $t['height']) / 2);

        return $t;
    }


    /**
     * Derives image type from mime type
     * 
     * @param  string $file File path or PHP tmp_name
     * @return string       Image type
     */
    protected function getType($file)
    {
        $mime = $this->getMimeType($file);
        return substr(strrchr($mime, "/"), 1);
    }


    /**
     * Abstraction wrapper for getimagesize
     * 
     * @see http://php.net/manual/en/function.getimagesize.php
     */
    protected function getSize($path)
    {
        return getimagesize($path);
    }


    /**
     * Abstraction wrapper for imagecreatefrom<type> functions
     *
     * @param  string $path File path
     * @param  string $type Image type, will try to get if not provided
     * @return object       Image resource
     */
    protected function createFrom($path, $type=null)
    {
        if (!$type) {
            $type = $this->getType($path);
        }
        $method = 'imagecreatefrom' . $type;

        return $method($path);
    }


    /**
     * Abstraction wrapper for imagecreatetruecolor
     * 
     * @see http://php.net/manual/en/function.imagecreatetruecolor.php
     * @param  int $width
     * @param  int $height
     * @return void
     */
    protected function createNew($width, $height)
    {
        return imagecreatetruecolor($width, $height);
    }


    /**
     * Abstraction wrapper for imagecopyresized
     * 
     * @see http://php.net/manual/en/function.imagecopyresized.php
     * @param  array $t Thumbnail properties
     * @param  array $o Original properties
     * @return void
     */
    protected function Resize(array $t, array $o)
    {
        imagecopyresized($t['resource'], $o['resource'], $t['x'], $t['y'],
                         $o['x'], $o['y'], $t['width'], $t['height'],
                         $o['width'], $o['height']);
    }


    /**
     * Abstraction wrapper for image<type> functions
     * 
     * @param  array $thumb Thumbnail properties
     * @return void
     */
    protected function writeImage(array $thumb)
    {
        $func = 'image' . $thumb['type'];
        $func($thumb['resource'], $thumb['path']);
    }
}
