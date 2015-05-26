<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Provides standard methods for creating SQL queries using safe PDO bindings.
 * Coverage of edge cases is not completely provided, but 99% of standard db
 * queries should use these methods instead of hard-coded queries.
 */
interface IDatabase
{

    /**
     * Initiates build a new query of a given type
     * 
     * @param  string $type  Type of query. Must minimally support: create,
     *                       delete, insert, update
     * @param  string $table Database table for query
     * @param  array  $args  Keyword arguments for query creation
     * @return object        $this
     * @throws \DomainException if $type is unsupported
     */
    public function newQuery($type, $table, array $args=null);


    /**
     * Sets current query to provided value
     * 
     * @param  string $query
     * @return object        $this
     */
    public function setQuery($query);


    /**
     * Returns the current query
     * 
     * @return string
     */
    public function returnQuery();


    /**
     * Creates a PDO prepared statement from the current query
     * 
     * @return object \rakelley\jhframe\interfaces\services\IStatementAbstractor
     */
    public function makeStatement();


    /**
     * Begins the creation of a where statement to add to the query
     * 
     * @param  string $operator
     *     @see \rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder::newWhere
     * @return object           \rakelley\jhframe\interfaces\services\IDatabaseWhereBuilder
     */
    public function addWhere($operator=null);


    /**
     * Begins the creation of a join statement to add to the query
     * 
     * @param  string $table
     *     @see \rakelley\jhframe\interfaces\services\IDatabaseJoinBuilder::newJoin
     * @param  string $type
     *     @see \rakelley\jhframe\interfaces\services\IDatabaseJoinBuilder::newJoin
     * @return object           \rakelley\jhframe\interfaces\services\IDatabaseJoinBuilder
     */
    public function addJoin($table, $type=null);


    /**
     * Add an order by statement to the query for one or both directions
     * 
     * @param  array  $args Key is direction (ASC/DESC), Value is string column
     *                      name or array of same
     * @return object       $this
     */
    public function addOrder(array $args);


    /**
     * Add a limit statement to the query
     * 
     * @param  mixed $limit Int or array of same to be comma separated
     * @return object       $this
     */
    public function addLimit($limit);


    /**
     * Strip all backticks (`) from query
     * 
     * @return object    $this
     */
    public function stripTicks();


    /**
     * Append string to query
     * 
     * @param  string $string
     * @return object         $this
     */
    public function Append($string);


    /**
     * Prepend string to query
     * 
     * @param  string $string
     * @return object         $this
     */
    public function Prepend($string);
}
