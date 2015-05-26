<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Class for fetching user input with standard sanitize and validate options
 */
class Input implements \rakelley\jhframe\interfaces\services\IInput
{
    use \rakelley\jhframe\traits\ConfigAware;

    /**
     * Default rulesets for common input types, defined in app config
     * @var array
     */
    protected $defaultRules = [];
    /**
     * Value filtering service instance
     * @var object
     */
    protected $filter;
    /**
     * Input/Output service instance
     * @var object
     */
    protected $io;
    /**
     * Table of sanitized and validated values for current Get call
     * @var array
     */
    protected $values = [];


    function __construct(
        \rakelley\jhframe\interfaces\services\IFilter $filter,
        \rakelley\jhframe\interfaces\services\IIo $io
    ) {
        $this->filter = $filter;
        $this->io = $io;

        $rules = $this->getConfig()->Get('APP', 'input_rules');
        $this->defaultRules = ($rules) ?: [];
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IInput::getList
     */
    public function getList(array $list, $method, $optional=false)
    {
        $this->values = [];
        $table = $this->io->getInputTable($method);

        array_walk(
            $list,
            function($rules, $key) use ($table, $optional) {
                if ($rules === 'default') {
                    if (isset($this->defaultRules[$key])) {
                        $rules = $this->defaultRules[$key];
                    } else {
                        throw new \RuntimeException(
                            'No Default Sanitizing Rules For: ' . $key,
                            500
                        );
                    }
                } elseif ($rules === '') {
                    $rules = [];
                }

                $value = $this->getValue($table, $key, $rules, $optional);

                if (isset($value)) {
                    $this->values[$key] = $value;
                }
            }
        );

        return $this->values;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IInput::searchKeys
     */
    public function searchKeys($pattern, $method, array $rules=null)
    {
        $table = $this->io->getInputTable($method);

        $values = array_intersect_key(
            $table,
            array_flip(preg_grep($pattern, array_keys($table)))
        );

        if ($rules && $values) {
            $key = 'by Key Pattern "' . $pattern . '"';
            $this->values = array_map(
                function($value) use ($key, $rules) {
                    return $this->applyRules($key, $value, $rules);
                },
                $values
            );
        } else {
            $this->values = ($values) ?: null;
        }

        return $this->values;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IInput::searchValues
     */
    public function searchValues($pattern, $method, array $rules=null)
    {
        $table = $this->io->getInputTable($method);

        $values = preg_grep($pattern, $table);

        if ($rules && $values) {
            $key = 'by Pattern "' . $pattern . '"';
            $this->values = array_map(
                function($value) use ($key, $rules) {
                    return $this->applyRules($key, $value, $rules);
                },
                $values
            );
        } else {
            $this->values = ($values) ?: null;
        }

        return $this->values;
    }


    /**
     * Get a value from inputtable and apply rules to it
     * @param  array   $table    array to find value in
     * @param  string  $key      key for value
     * @param  array   $rules    rules to apply to value (if any)
     * @param  boolean $optional true if value is not required
     * @return mixed             mixed value if found and passes rules or null if not
     */
    protected function getValue(array $table, $key, array $rules, $optional)
    {
        if (isset($table[$key]) &&
           (is_array($table[$key]) || strlen($table[$key]) > 0)
        ) {
            $value = $table[$key];
        } elseif (isset($rules['defaultvalue'])) {
            $value = $rules['defaultvalue'];
        } elseif (!$optional) {
            throw new InputException('Required Field ' . $key . ' Missing');
        } else {
            return null;
        }

        if ($rules) {
            $value = $this->applyRules($key, $value, $rules);
        }

        return $value;
    }


    /**
     * Apply rules to and return value
     *
     * @throws \rakelley\jhframe\classes\InputException if a rule fails
     * @param  string $key   Key for value being validated/sanitized
     * @param  mixed  $value Value to validate/sanitize
     * @param  array  $rules Rules to apply to value
     * @return mixed         Validated/sanitized value
     */
    protected function applyRules($key, $value, $rules)
    {
        if (isset($rules['filters'])) {
            $value = $this->filter->Filter($value, $rules['filters']);
            if (!isset($value)) {
                throw new InputException('Required ' . $key . ' Missing');
            }
        }

        if (isset($rules['equalto']) &&
            $value !== $this->values[$rules['equalto']]
        ) {
            throw new InputException($key . ' Must Equal ' . $rules['equalto']);
        }

        if (isset($rules['minlength']) &&
            strlen($value) < $rules['minlength']
        ) {
            throw new InputException(
                $key . ' Must Be At Least ' . $rules['minlength'] . ' Characters'
            );
        }

        if (isset($rules['maxlength']) &&
            strlen($value) > $rules['maxlength']
        ) {
            throw new InputException(
                $key . ' Must Be At Most ' . $rules['maxlength'] . ' Characters'
            );
        }

        return $value;
    }
}
