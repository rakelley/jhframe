<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * View type which contains a HTML form and is associated with a Form route.
 * Classes extending this should generally be very thin, only needing to specify
 * their properties and using inherited methods to construct.
 */
abstract class FormView extends View implements
    \rakelley\jhframe\interfaces\view\IFormView
{
    /**
     * HTML attribtes to add to <form> tag as key="value"
     * @var array
     */
    protected $attributes;
    /**
     * IFormBuilder instance
     * @var object
     */
    protected $builder;
    /**
     * Data storage for binding to form fields
     * @var array
     */
    protected $data = [];
    /**
     * @see \rakelley\jhframe\interfaces\view\IFormView::getFields
     * @var array
     */
    protected $fields;
    /**
     * Form's title, if any.
     * @see \rakelley\jhframe\interfaces\services\IFormBuilder::constructTitle
     * @var mixed
     */
    protected $title = null;


    function __construct(
        \rakelley\jhframe\interfaces\services\IFormBuilder $builder
    ) {
        $this->builder = $builder;
    }


    /**
     * @see \rakelley\jhframe\interfaces\view\IFormView::getFields
     */
    public function getFields()
    {
        return $this->fields;
    }


    /**
     * @see \rakelley\jhframe\interfaces\view\IFormView::getMethod
     */
    public function getMethod()
    {
        return $this->attributes['method'];
    }


    /**
     * Default implementation of constructView for no successMsg
     * 
     * @see \rakelley\jhframe\classes\View::constructView
     */
    public function constructView()
    {
        $this->viewContent = $this->standardConstructor();
    }


    /**
     * Standard method for generating form html
     * 
     * @param  string $successMsg Optional string to show on form successful
     *                            submit
     * @return string             Generated view markup
     */
    protected function standardConstructor($successMsg=null)
    {
        $formAttr = $this->builder->combineAttributes($this->attributes);

        $status = $this->builder->constructStatusBlock($successMsg);

        $title = ($this->title) ? $this->builder->constructTitle($this->title) :
                 '';

        $fields = implode('', array_map(
            [$this, 'constructField'],
            $this->fields
        ));

        $view = <<<HTML
<form {$formAttr}>
    {$status}
    {$title}
    <fieldset>
        {$fields}
    </fieldset>
</form>

HTML;

        return $view;
    }


    /**
     * Generates markup for single field using IFormBuilder or custom method
     * 
     * @param  array $rules arguments for field construction
     * @return string
     */
    protected function constructField(array $rules)
    {
        if (isset($rules['method'])) {
            $method = $rules['method'];
            return $this->$method();
        }

        if (isset($rules['data-binding'])) {
            $data = $this->fillData($rules['data-binding']);
        } else {
            $data = null;
        }

        switch ($rules['type']) {
            case 'textarea':
                return $this->builder->constructTextarea($rules, $data);
                break;

            case 'password':
                return $this->builder->constructPassword($rules, $data);
                break;

            case 'select':
                if (isset($rules['selected'])) {
                    $select = $rules['selected'];
                } elseif (isset($rules['selected-data'])) {
                    $select = $this->fillData($rules['selected-data']);
                } else {
                    $select = null;
                }
                return $this->builder->constructSelect($rules, $data, $select);
                break;

            case 'checkbox':
                return $this->builder->constructCheckbox($rules, $data);
                break;

            default:
                return $this->builder->constructInput($rules, $data);
                break;
        }
    }


    /**
     * Gets data for field by checking parameters and class data for key.
     * Supports multi-level keys, e.g. "['foo']['bar']".
     * 
     * @param  string $key Array key expected
     * @return mixed       Value for key
     * @throws \RuntimeException If key not found
     */
    protected function fillData($key)
    {
        if (count(explode('][', $key)) > 1) {
            return $this->getMultiLevelData(explode('][', $key));
        } else {
            if (isset($this->parameters) && isset($this->parameters[$key])) {
                return $this->parameters[$key];
            } elseif (in_array($key, array_keys($this->data))) {
                return ($this->data[$key] !== null) ? $this->data[$key] : '';
            } else {
                throw new \RuntimeException(
                    'Data-Binding Not Provided For ' . $key,
                    500
                );
            }
        }
    }


    /**
     * Steps down through multi-level array to get deep value
     * 
     * @param  array $keys Keys to step through
     * @return mixed       Value for last key
     */
    protected function getMultiLevelData(array $keys)
    {
        $lastKey = count($keys) - 1;
        $keys[$lastKey] = str_replace(']', '', $keys[$lastKey]);
        $firstVal = str_replace('[', '', $keys[0]);
        unset($keys[0]);

        $value = $this->fillData($firstVal);

        foreach ($keys as $k) {
            $value = $value[$k];
        }

        return (($value !== null) ? $value : '');
    }
}
