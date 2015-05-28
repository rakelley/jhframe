<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Base class for database table models, includes basic properties and
 * metaproperty trait
 */
abstract class Model
{
    use \rakelley\jhframe\traits\MetaProperties;

    /**
     * Columns for table
     * @var array
     */
    protected $columns = [];
    /**
     * IDatabase instance, used to construct and perform queries
     * @var object
     */
    protected $db;
    /**
     * Primary key(s) for table
     * @var string|array
     */
    protected $primary = null;
    /**
     * Database table name for model
     * @var string
     */
    protected $table = '';


    /**
     * @param \rakelley\jhframe\interfaces\services\IDatabase $db
     */
    function __construct(
        \rakelley\jhframe\interfaces\services\IDatabase $db
    ) {
        $this->db = $db;
    }
}
