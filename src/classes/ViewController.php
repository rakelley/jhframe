<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

use \rakelley\jhframe\interfaces\ITakesParameters;
use \rakelley\jhframe\interfaces\view\IHasMetaData;
use \rakelley\jhframe\interfaces\view\IHasSubViews;
use \rakelley\jhframe\interfaces\view\IRequiresData;

/**
 * Default implementation for IViewController
 */
class ViewController implements \rakelley\jhframe\interfaces\services\IViewController
{
    use \rakelley\jhframe\traits\GetsServerProperty,
        \rakelley\jhframe\traits\ServiceLocatorAware;

    /**
     * Cache instance
     * @var object
     */
    protected $cache;
    /**
     * IRenderable instance
     * @var object
     */
    protected $renderable;


    /**
     * @param \rakelley\jhframe\interfaces\IRenderable           $renderable
     * @param \rakelley\jhframe\interfaces\services\IKeyValCache $cache
     */
    function __construct(
        \rakelley\jhframe\interfaces\IRenderable $renderable,
        \rakelley\jhframe\interfaces\services\IKeyValCache $cache
    ) {
        $this->renderable = $renderable;
        $this->cache = $cache;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IViewController::createView()
     */
    public function createView($viewName, array $parameters=null,
                               $cacheable=false)
    {
        $cacheKey = $this->getCacheKey($viewName);

        if ($cacheable && $this->cache->Read($cacheKey)) {
            $viewProperties = $this->cache->Read($cacheKey);
        } else {
            $viewProperties = $this->buildView($viewName, $parameters);
            if ($cacheable) {
                $this->cache->Write($viewProperties, $cacheKey);
            }
        }

        return $this->toRenderable($viewProperties);
    }


    /**
     * Creates view instance and constructs view according to interfaces if any
     * 
     * @param  string     $viewName   Qualified class name of view
     * @param  array|null $parameters Parameters to pass to view if any
     * @return object                 \rakelley\jhframe\interfaces\IRenderable
     */
    protected function buildView($viewName, array $parameters=null)
    {
        $viewObject = $this->getLocator()->Make($viewName);

        if ($parameters && $viewObject instanceof ITakesParameters) {
            $viewObject->setParameters($parameters);
        }
        if ($viewObject instanceof IRequiresData) {
            $viewObject->fetchData();
        }
        if ($viewObject instanceof IHasSubViews) {
            $viewObject->makeSubViews();
        }
        $viewObject->constructView();
        if ($viewObject instanceof IHasMetaData) {
            $viewObject->fetchMetaData();
        }

        return $viewObject->returnView();
    }


    /**
     * Converts view properties array to a Renderable
     * 
     * @param  array $properties
     * @return object            \rakelley\jhframe\interfaces\IRenderable
     */
    protected function toRenderable(array $properties)
    {
        $renderable = $this->renderable->getNewInstance();

        $renderable->setContent($properties['content'])
                   ->setType($properties['type'])
                   ->setMetaData($properties['meta']);

        return $renderable;
    }


    /**
     * Creates cache key for request-cacheable views
     * 
     * @param  string $viewName
     * @return string
     */
    protected function getCacheKey($viewName)
    {
        $prefix = ($this->getServerProp('REQUEST_URI')) ?: 'LOCAL';

        return $prefix . '__' . $viewName;
    }
}
