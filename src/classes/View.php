<?php
/**
 * @package jhframe
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
     * The expected implementation for children of this class is to pass the
     * constructed content to the viewContent property
     *
     * @see \rakelley\jhframe\interfaces\view\IView::constructView()
     */
    abstract public function constructView();


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\view\IView::returnView()
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
