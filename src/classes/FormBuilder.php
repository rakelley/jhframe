<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Default implementation for IFormBuilder
 */
class FormBuilder implements \rakelley\jhframe\interfaces\services\IFormBuilder
{

    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFormBuilder::combineAttributes()
     */
    public function combineAttributes(array $attributes)
    {
        $list = implode(' ', array_map(
            function($k, $v) {
                return $k . '="' . $v . '"';
            },
            array_keys($attributes),
            array_values($attributes)
        ));

        return ' ' . $list;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFormBuilder::constructCheckbox()
     */
    public function constructCheckbox(array $rules, $data=null)
    {
        $attr = (isset($rules['attr'])) ?
                $this->combineAttributes($rules['attr']) : '';

        $input = '<input type="checkbox"' . $attr . ' />';

        return $this->constructLabel($rules, $input);
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFormBuilder::constructInput()
     */
    public function constructInput(array $rules, $data=null)
    {
        $label = $this->constructLabel($rules);

        $rules['attr']['type'] = $rules['type'];
        if ($data) {
            $rules['attr']['value'] = $data;
        }
        $attr = $this->combineAttributes($rules['attr']);
        if (isset($rules['required'])) {
            $attr .= ' required';
        }
        if (isset($rules['autofocus'])) {
            $attr .= ' autofocus';
        }

        return $label . '<input' . $attr . ' />';
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFormBuilder::constructLabel()
     */
    public function constructLabel(array $rules, $input='')
    {
        if (!isset($rules['label'])) {
            return $input;
        }

        $for = (isset($rules['attr']['name'])) ?
               ' for="' . $rules['attr']['name'] . '"' : '';

        return '<label' . $for . '>' . $input .  $rules['label'] . '</label>';
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFormBuilder::constructPassword()
     */
    public function constructPassword(array $rules, $data=null)
    {
        $input = $this->constructInput($rules, $data);

        if (strpos($input, 'valNewPassword') !== false) {
            $input .= <<<HTML
<div class="password_strength">
    Your password can be brute-forced in: <span data-passwordtime></span>
</div>

HTML;
        }

        return $input;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFormBuilder::constructSelect()
     */
    public function constructSelect(array $rules, $data=null, $selected=null)
    {
        $label = $this->constructLabel($rules);

        $attr = (isset($rules['attr'])) ?
                $this->combineAttributes($rules['attr']) : '';
        if (isset($rules['required'])) {
            $attr .= ' required';
        }

        $options = (isset($rules['options'])) ? $rules['options'] : [];
        if ($data) {
            $options = array_merge($options, $data);
        }
        $optionList = $this->combineOptions($options, $selected);

        return $label . '<select' . $attr . '>' . $optionList . '</select>';
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFormBuilder::constructStatusBlock()
     */
    public function constructStatusBlock($message=null)
    {
        $status = '<ul class="form-status-error"></ul>';
        if ($message) {
            $status .= '<p class="form-status-success">' . $message . '</p>';
        }

        return $status;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFormBuilder::constructTextarea()
     */
    public function constructTextarea(array $rules, $data=null)
    {
        $label = $this->constructLabel($rules);

        $attr = (isset($rules['attr'])) ?
                $this->combineAttributes($rules['attr']) : '';
        if (isset($rules['required'])) {
            $attr .= ' required';
        }
        if (isset($rules['autofocus'])) {
            $attr .= ' autofocus';
        }

        $content = ($data) ?: '';

        return $label . '<textarea' . $attr . '>' . $content . '</textarea>';
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IFormBuilder::constructTitle()
     */
    public function constructTitle($title)
    {
        if (is_array($title)) {
            $markup = <<<HTML
<p class="form-title">{$title['title']}</p>
<p class="form-title-sub">{$title['sub']}</p>
HTML;
        } else {
            $markup = '<p class="form-title">' . $title . '</p>';
        }

        return $markup;
    }


    /**
     * Generates markup for each <option> tag and combines them.
     * If array key is numeric, array value will be used as both option text and
     * option value, otherwise array key will be used as option value and array
     * value will be used as option text.
     * An array key of 'empty' is a special case which will result in an empty
     * option value.
     * 
     * @param  array       $options
     * @param  string|null $selected Optional value to match for 'selected'
     *                               attribute.  Will only match first case.
     * @return string
     */
    protected function combineOptions(array $options, $selected)
    {
        $selectFlag = !!$selected;

        return implode('', array_map(
            function($text, $value) use (&$selectFlag, $selected) {
                if (!is_string($value)) { // case for numeric array key
                    $value = $text;
                }
                if ($value === 'empty') {
                    $value = '';
                }
                if ($selectFlag && $value === $selected) {
                    $selAttr = ' selected';
                    $selectFlag = false;
                } else {
                    $selAttr = '';
                }

                return '<option value="' . $value . '"' . $selAttr . '>' .
                       $text . '</option>';
            },
            array_values($options),
            array_keys($options)
        ));
    }
}
