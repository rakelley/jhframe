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
 * Interface for any renderable resource
 */
interface IRenderable extends \rakelley\jhframe\interfaces\IReproducible
{

    /**
     * Get stored content
     * 
     * @return mixed
     */
    public function getContent();

    /**
     * Set stored content
     * 
     * @param  mixed $content
     * @return object         $this
     */
    public function setContent($content);


    /**
     * Get content type
     * 
     * @return string|null
     */
    public function getType();

    /**
     * Set content type
     *
     * @see \rakelley\jhframe\interfaces\services\IRenderer
     * @param  string $type
     * @return object         $this
     */
    public function setType($type);


    /**
     * Get content metadata
     * 
     * @return array|null
     */
    public function getMetaData();

    /**
     * Set content metadata
     * 
     * @param  array|null $data
     * @return object         $this
     */
    public function setMetaData($data);


    /**
     * Render renderable to user
     */
    public function Render();
}
