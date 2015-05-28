<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes\resources;

/**
 * Renderable Resource for storing the result of an Action or other API call
 */
class ActionResult extends \rakelley\jhframe\classes\Renderable
{
    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\classes\Renderable::$meta
     */
    protected $meta = null;
    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\classes\Renderable::$type
     */
    protected $type = \rakelley\jhframe\interfaces\services\IRenderer::TYPE_API;
    /**
     * Stored success value
     * @var boolean
     */
    private $success;
    /**
     * Stored error if any
     * @var string
     */
    private $error;
    /**
     * Stored message if any
     * @var mixed
     */
    private $message;


    /**
     * Getter for success value
     * 
     * @return boolean|null
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Setter for success value
     * 
     * @param  boolean $success
     * @return object           $this
     */
    public function setSuccess($success)
    {
        $this->success = $success;
        return $this;
    }


    /**
     * Getter for error value
     * 
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Setter for error value
     * 
     * @param  string $error
     * @return object        $this
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }


    /**
     * Getter for message value
     * 
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Setter for message value
     * 
     * @param  mixed $message
     * @return object         $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IRenderable::getContent()
     *
     * In most cases content property should be unset and default
     * success/error/message array provided
     */
    public function getContent()
    {
        if (isset($this->content)) {
            return $this->content;
        } else {
            $content = ['success' => $this->getSuccess()];
            if ($this->getError() !== null) {
                $content['error'] = $this->getError();
            }
            if ($this->getMessage() !== null) {
                $content['message'] = $this->getMessage();
            }

            return $content;
        }
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IRenderable::setType()
     */
    public function setType($type)
    {
        throw new \BadMethodCallException(
            'ActionResult has a fixed Type value',
            500
        );
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\IRenderable::setMetaData()
     */
    public function setMetaData($data)
    {
        throw new \BadMethodCallException(
            'ActionResult has a fixed MetaData value',
            500
        );
    }
}
