<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Generic default implementation of IRenderable
 */
class Renderable implements \rakelley\jhframe\interfaces\IRenderable
{
    use \rakelley\jhframe\traits\Reproducible,
        \rakelley\jhframe\traits\ServiceLocatorAware;

    /**
     * Stored content value
     * @var mixed
     */
    protected $content;
    /**
     * Stored metadata value
     * @var array
     */
    protected $meta;
    /**
     * Renderer service instance
     * @var object
     */
    protected $renderer;
    /**
     * Stored type value
     * @var string
     */
    protected $type;


    function __construct(
        \rakelley\jhframe\interfaces\services\IRenderer $renderer
    ) {
        $this->renderer = $renderer;
    }


    /**
     * @see \rakelley\jhframe\interfaces\IRenderable::getContent
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @see \rakelley\jhframe\interfaces\IRenderable::setContent
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\IRenderable::getMetaData
     */
    public function getMetaData()
    {
        return $this->meta;
    }

    /**
     * @see \rakelley\jhframe\interfaces\IRenderable::setMetaData
     */
    public function setMetaData($data)
    {
        $this->meta = $data;
        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\IRenderable::getType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @see \rakelley\jhframe\interfaces\IRenderable::setType
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\IRenderable::Render
     */
    public function Render()
    {
        $this->renderer->Render($this);
    }
}
