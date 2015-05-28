<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Default implementation for IFormValidator
 */
class FormValidator implements
    \rakelley\jhframe\interfaces\services\IFormValidator
{
    use \rakelley\jhframe\traits\GetsInput,
        \rakelley\jhframe\traits\ServiceLocatorAware;

    /**
     * Bucket for optional fields
     * @var array
     */
    protected $accepts;
    /**
     * Bucket for file upload fields
     * @var array
     */
    protected $files;
    /**
     * Bucket for required fields
     * @var array
     */
    protected $requires;
    /**
     * View to retrieve fields and method from
     * @var object
     */
    protected $view;


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFormValidator::Validate()
     */
    public function Validate(\rakelley\jhframe\interfaces\view\IFormView $view)
    {
        $this->setView($view);
        $fields = $view->getFields();
        $method = $view->getMethod();

        array_walk($fields, [$this, 'sortField']);

        $input = [];
        if ($this->requires) {
            $input = $this->getInput($this->requires, $method);
        }
        if ($this->accepts) {
            $accepted = $this->getInput($this->accepts, $method, true);
            $input = array_merge($input, $accepted);
        }
        if ($this->files) {
            $uploaded = $this->getInput($this->files, 'files');
            $input = array_merge($input, $uploaded);
        }

        return $input;
    }


    /**
     * Internal setter for view object, also resets state
     *
     * @param  object $view
     * @return void
     */
    protected function setView(\rakelley\jhframe\interfaces\view\IFormView $view)
    {
        $this->view = $view;

        $this->accepts = [];
        $this->files = [];
        $this->requires = [];
    }


    /**
     * Sorts a field from the FormView into an appropriate bucket if matching
     * user input needs to be checked for.
     * 
     * @param  array  $field Field properties
     * @param  string $key   Field name
     * @return void
     */
    protected function sortField(array $field, $key)
    {
        if (isset($field['type']) && $field['type'] === 'file') {
            $this->files[$key] = '';
        } elseif (!isset($field['sanitize'])) {
            return;
        } elseif (isset($field['required'])) {
            $this->requires[$key] = $field['sanitize'];
        } else {
            $this->accepts[$key] = $field['sanitize'];
        }
    }
}
