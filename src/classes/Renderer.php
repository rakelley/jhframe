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
 * Default implementation of IRenderer
 */
class Renderer implements \rakelley\jhframe\interfaces\services\IRenderer
{
    use \rakelley\jhframe\traits\ConfigAware,
        \rakelley\jhframe\traits\ServiceLocatorAware;

    /**
     * Real value to replace IRenderer::TYPE_API
     * @var string
     */
    protected $apiType = self::TYPE_JSON;
    /**
     * Default type value to use if none provided and not an API call
     * @var string
     */
    protected $defaultType = self::TYPE_PAGE;
    /**
     * Whether the current request is ajax, used to possibly override content
     * type
     * @var boolean
     */
    protected $isAjax;
    /**
     * IIo service instance
     * @var object
     */
    protected $io;


    function __construct(
        \rakelley\jhframe\interfaces\services\IIo $io
    ) {
        $this->io = $io;

        $this->isAjax = $this->getConfig()->Get('ENV', 'is_ajax');
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IRenderer::Render
     */
    public function Render(\rakelley\jhframe\interfaces\IRenderable $renderable)
    {
        if ($renderable->getContent() === null) {
            throw new \DomainException(
                'Attempt to Render Renderable With No Content',
                500
            );
        }

        $content = $this->handleType($renderable);

        $this->io->toEcho($content);
    }


    /**
     * Makes any necessary changes to content and sends any appropriate headers
     * based on content type
     * 
     * @param  object $renderable Renderable to handle type for
     * @return string             Prepared content
     */
    protected function handleType(\rakelley\jhframe\interfaces\IRenderable $renderable)
    {
        $content = $renderable->getContent();

        $type = $renderable->getType();
        if ($this->isAjax || $type === self::TYPE_API) {
            $type = $this->apiType;
        } elseif (!isset($type) || $type === self::TYPE_DEFAULT) {
            $type = $this->defaultType;
        }

        switch ($type) {
            case self::TYPE_JSON:
                if (!$this->isJson($content)) {
                    $content = json_encode($content);
                }
                $this->io->Header('content-type: application/json');
                break;
            case self::TYPE_XML:
                $this->io->Header('content-type: application/xml');
                break;
            case self::TYPE_PAGE:
                $content = $this->makeComposite($content,
                                                $renderable->getMetaData());
                break;
            default:
                break;
        }

        return $content;      
    }


    /**
     * Checks if content is JSON so it's not double-encoded
     * 
     * @param  mixed   $content
     * @return boolean
     */
    protected function isJson($content)
    {
        if (!is_string($content)) {
            return false;
        }

        $decoded = json_decode($content, true);
        return (json_last_error() === JSON_ERROR_NONE);
    }


    /**
     * Composes page content with a lazy-loaded Template.
     * 
     * @param  string     $content Page content
     * @param  array|null $meta    Optional metadata/parameters for template
     * @return string              Composite content
     */
    protected function makeComposite($content, $meta)
    {
        return $this->getLocator()->Make('rakelley\jhframe\interfaces\ITemplate')
                                  ->makeComposite($content, $meta);
    }
}
