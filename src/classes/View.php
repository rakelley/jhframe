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
 * Default abstract parent class for IView implementers
 * @abstract
 */
abstract class View implements \rakelley\jhframe\interfaces\view\IView
{
    /**
     * Generated view
     * @var string
     */
    protected $viewContent = null;
    /**
     * Optional metadata for view
     * @var array
     */
    protected $metaData = [];
    /**
     * View content type
     * @var string
     */
    protected $contentType =
        \rakelley\jhframe\interfaces\services\IRenderer::TYPE_DEFAULT;


    /**
     * @see \rakelley\jhframe\interfaces\view\IView::constructView
     * The expected implementation for children of this class is to pass the
     * constructed content to the viewContent property
     */
    abstract public function constructView();


    /**
     * @see \rakelley\jhframe\interfaces\view\IView::returnView
     */
    public function returnView()
    {
        if (!$this->viewContent) {
            throw new \BadMethodCallException(
                'Return Called With No ViewContent',
                500
            );
        }

        return [
            'content' => $this->viewContent,
            'type' => $this->contentType,
            'meta' => $this->metaData,
        ];
    }
}
