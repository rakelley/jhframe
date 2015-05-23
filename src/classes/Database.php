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
 * Default implementation of IDatabase
 */
class Database implements \rakelley\jhframe\interfaces\services\IDatabase
{
    /**
     * IJoinBuilder instance
     * @var object
     */
    protected $joinBuilder;
    /**
     * Current generated query
     * @var string
     */
    protected $query = '';
    /**
     * IStatementAbstractor instance
     * @var object
     */
    protected $statementService;
    /**
     * IWhereBuilder instance
     * @var object
     */
    protected $whereBuilder;


    function __construct(
        \rakelley\jhframe\interfaces\services\IDatabaseJoinBuilder $joinBuilder,
        \rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder $whereBuilder,
        \rakelley\jhframe\interfaces\services\IStatementAbstractor $statementService
    ) {
        $this->joinBuilder = $joinBuilder;
        $this->whereBuilder = $whereBuilder;
        $this->statementService = $statementService;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::newQuery
     */
    public function newQuery($type, $table, array $args=null)
    {
        $this->query = '';
        $method = 'create' . $type;

        if (!method_exists($this, $method)) {
            throw new \DomainException('Query Type ' . $type . ' Not Supported',
                                       500);
        } else {
            $this->query = $this->$method($table, $args);
            return $this;
        }
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::setQuery
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::returnQuery
     */
    public function returnQuery()
    {
        return $this->query;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::makeStatement
     */
    public function makeStatement()
    {
        return $this->statementService->makeStatement($this->query);
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::addWhere
     */
    public function addWhere($operator=null)
    {
        return $this->whereBuilder->newWhere($this, $operator);
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::addJoin
     */
    public function addJoin($table, $type=null)
    {
        return $this->joinBuilder->newJoin($this, $table, $type);
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::addOrder
     */
    public function addOrder(array $args)
    {
        $this->query .= ' ORDER BY ' . implode(', ', array_map(
            function($dir, $columns) {
                return '`' . implode('`,`', (array) $columns) . '` ' . $dir;
            },
            array_keys($args),
            array_values($args)
        ));

        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::addLimit
     */
    public function addLimit($limit)
    {
        $this->query .= ' LIMIT ' . implode(',', (array) $limit);

        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::stripTicks
     */
    public function stripTicks()
    {
        $this->query = str_replace('`', '', $this->query);

        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::Append
     */
    public function Append($string)
    {
        $this->query .= $string;

        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IDatabase::Prepend
     */
    public function Prepend($string)
    {
        $this->query = $string . $this->query;

        return $this;
    }


    /**
     * Create a new Select statement
     * 
     * @param  string $table  Name of database table
     * @param  array  $args   Keyword arguments
     *     mixed   'select'   Optional string selector or array of same
     *     boolean 'distinct' True if using DISTINCT
     * @return string         Query
     */
    protected function createSelect($table, array $args=null)
    {
        if (isset($args['select'])) {
            $select = "`" . implode("`,`", (array) $args['select']) . "`";
            unset($args['select']);
        } else {
            $select = '*';
        }
        if (isset($args['distinct'])) {
            $select = 'DISTINCT ' . $select;
        }
                  
        return "SELECT $select FROM `$table`";
    }


    /**
     * Create a new Insert statement
     * 
     * @param  string $table Name of database table
     * @param  array  $args  Keyword arguments
     *     array 'columns'   Array of column names to be inserted and used as
     *                       param placeholders
     *     int   'rows'      Optional row count for multi-row insert
     * @return string        Query
     */
    protected function createInsert($table, array $args=null)
    {
        $columns = "(`" . implode("`,`", $args['columns']) . "`)";

        if (isset($args['rows'])) {
            $filler = '('
                    . implode(',', array_fill(0, count($args['columns']), '?'))
                    . ')';
            $values = implode(', ', array_fill(0, $args['rows'], $filler));
        } else {
            $values = '(:' . implode(',:', $args['columns']) . ')';
        }

        return "INSERT INTO `$table` $columns VALUES $values";
    }


    /**
     * Create a new Update statement
     * 
     * @param  string $table Name of database table
     * @param  array  $args  Keyword arguments
     *     array 'columns'   Array of column names to be updated and used as
     *                       param placeholders
     * @return string        Query
     */
    protected function createUpdate($table, array $args=null)
    {
        $columnList = implode(', ', array_map(
            function ($column) {
                return "`$column`=:$column";
            },
            $args['columns']
        ));

        return "UPDATE `$table` SET $columnList";
    }


    /**
     * Create a new Delete statement
     * 
     * @param  string $table Name of database table
     * @param  array  $args  Keyword arguments
     *                       no arguments for this function
     * @return string        Query
     */
    protected function createDelete($table, array $args=null)
    {
        return "DELETE FROM `$table`";
    }
}
