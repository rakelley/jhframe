<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Sanitizing library, default implementation of IFilter
 */
class Filter implements \rakelley\jhframe\interfaces\services\IFilter
{
    /**
     * Tidy instance
     * @var object
     */
    protected $tidy;
    /**
     * Default configuration values for Tidy::repairString
     * @var array
     */
    protected $defaultTidyConfig = [
        'clean'          => true, 
        'output-xhtml'   => true, 
        'show-body-only' => true, 
        'drop-font-tags' => true,
        'merge-divs'     => false,
        'wrap'           => 0
    ];


    function __construct(
        \Tidy $tidy
    ) {
        $this->tidy = $tidy;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::Filter
     */
    public function Filter($input, $filters)
    {
        $filters = (array) $filters;
        array_walk(
            $filters,
            function($v, $k) use (&$input) {
                if (is_string($k)) {
                    $input = $this->executeFilter($input, $k, $v);
                } else {
                    $input = $this->executeFilter($input, $v);
                }
            }
        );

        return ($input) ?: null;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::asList
     */
    public function asList($input, $args)
    {
        $list = explode($args['separator'], $input);
        $filters = $args['filters'];

        $filteredList = array_map(
            function($item) use ($filters) {
                return $this->Filter($item, $filters);
            },
            $list
        );
        $filteredList = array_filter($filteredList);

        return ($filteredList) ? implode($args['separator'], $filteredList) :
               null;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::Date
     */
    public function Date($input, $format=null)
    {
        $format = ($format) ?: 'Y-m-d H:i:s';
        try {
            $input = trim($input);
            $dt = new \DateTime($input);
            $date = $dt->format($format);
        } catch (\Exception $e) {
            $date = null;
        }

        return $date;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::Email
     */
    public function Email($input)
    {
        $input = iconv('UTF-8', 'UTF-8//TRANSLIT', $input);
        $input = trim($input);

        $input = filter_var($input, FILTER_SANITIZE_EMAIL);

        return (filter_var($input, FILTER_VALIDATE_EMAIL)) ? $input : null;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::Float
     */
    public function Float($input)
    {
        $input = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT,
                            FILTER_FLAG_ALLOW_FRACTION);

        return ($input !== '') ? (float) $input : null;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::encodeHtml
     */
    public function encodeHtml($input)
    {
        $input = iconv('UTF-8', 'UTF-8//TRANSLIT', $input);
        $input = trim($input);

        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);

        return ($input) ?: null;
    }

    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::decodeHtml
     */
    public function decodeHtml($input)
    {
        $input = iconv('UTF-8', 'UTF-8//TRANSLIT', $input);
        $input = trim($input);

        $input = htmlspecialchars_decode($input, ENT_QUOTES | ENT_HTML5);

        return ($input) ?: null;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::Int
     */
    public function Int($input)
    {
        if (is_float($input)) $input = round($input);
        $input = filter_var($input, FILTER_SANITIZE_NUMBER_INT);

        return ($input !== '') ? (int) $input : null;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::spaceToUnderscore
     */
    public function spaceToUnderscore($input)
    {
        $input = trim($input);
        $input = str_replace(' ', '_', $input);
        $input = preg_replace('/__+/', '_', $input);

        return ($input) ?: null;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::underscoreToSpace
     */
    public function underscoreToSpace($input)
    {
        $input = str_replace('_', ' ', $input);
        $input = preg_replace('/\s\s+/', ' ', $input);
        $input = trim($input);

        return ($input) ?: null;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::plainText
     */
    public function plainText($input)
    {
        $input = iconv('UTF-8', 'UTF-8//TRANSLIT', $input);
        $input = trim($input);

        $input = $this->Word($input, '\d\s!?.,-');

        return ($input) ?: null;
    }

    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::tidyText
     */
    public function tidyText($text, array $config=null)
    {
        $text = iconv('UTF-8', 'UTF-8//TRANSLIT', $text);
        $text = trim($text);

        $config = ($config) ? array_merge($this->defaultTidyConfig, $config) :
                              $this->defaultTidyConfig;

        $text = $this->tidy->repairString($text, $config, 'utf8');

        return ($text) ?: null;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::Url
     */
    public function Url($input)
    {
        $input = iconv('UTF-8', 'UTF-8//TRANSLIT', $input);

        $input = filter_var($input, FILTER_SANITIZE_URL);

        return ($input) ?: null;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IFilter::Word
     */
    public function Word($input, $permitted='')
    {
        $input = iconv('UTF-8', 'UTF-8//TRANSLIT', $input);
        $input = trim($input);

        $regex = '/[^\w' . $permitted . ']/i';
        $input = preg_replace($regex, '', $input);

        return ($input) ?: null;
    }


    protected function executeFilter($input, $filter, $arg=null)
    {
        if (method_exists($this, $filter)) {
            if (isset($arg)) {
                $input = $this->$filter($input, $arg);
            } else {
                $input = $this->$filter($input);
            }           
        } elseif (function_exists($filter)) {
            if (isset($arg)) {
                $input = $filter($input, $arg);
            } else {
                $input = $filter($input);
            }           
        } else {
            throw new \DomainException(
                'Filter ' . $filter . ' Does Not Exist',
                500
            );
        }


        return ($input) ?: null;
    }
}
