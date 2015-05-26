<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Interface for renderer service, defines constants for common content types
 */
interface IRenderer
{
    /**
     * Constant to signify content is generic API type.  Implementing class
     * should check for this constant and replace with appropriate real value.
     */
    const TYPE_API = 'API_CONTENT';
    /**
     * Constant to signify content is generic default type.  Implementing class
     * should check for this constant and replace with appropriate real value.
     */
    const TYPE_DEFAULT = 'DEFAULT_CONTENT';
    /**
     * Content type is JSON or should be encoded to JSON
     */
    const TYPE_JSON = 'json';
    /**
     * Content type is Page and content should be composited with a page
     * template
     */
    const TYPE_PAGE = 'page';
    /**
     * Content is plain HTML or plain text and should not be altered
     */
    const TYPE_PLAIN = 'plain';
    /**
     * Content type is XML
     */
    const TYPE_XML = 'xml';


    /**
     * Prepares and renders content according to type
     *
     * @param  object $renderable Renderable to render
     * @return void
     * @throws \DomainException if Renderable::getContent returns null
     */
    public function Render(\rakelley\jhframe\interfaces\IRenderable $renderable);
}
