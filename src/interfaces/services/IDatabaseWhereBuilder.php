<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Interface for class which handles construction of WHERE portions of queries
 */
interface IDatabaseWhereBuilder
{

    /**
     * Initiates new Where, leading with WHERE if operator is null or operator
     * 
     * @param  object $db       \rakelley\jhframe\interfaces\services\IDatabase
     * @param  string $operator AND or OR for subsequent wheres
     * @return object           $this
     */
    public function newWhere(IDatabase $db, $operator=null);


    /**
     * Adds a column equivalence to the where statement, or multiple if array
     * and operator provided, and appends it to the db query.
     * 
     * @param  mixed  $column   Single string column name or array of same
     * @param  string $operator Operator to join multiple columns
     * @return object           \rakelley\jhframe\interfaces\services\IDatabase
     */
    public function Equals($column, $operator=null);


    /**
     * Adds a placeholdered IN statement to where and appends it to the db query
     * 
     * @param  string $column Column name
     * @param  array  $values A placeholder ? will be added for each value
     * @return object         \rakelley\jhframe\interfaces\services\IDatabase
     */
    public function In($column, array $values);


    /**
     * Adds a LIKEs to the where statement, or multiple if array and operator
     * provided, and appends it to the db query.
     * 
     * @param  string $column      Column name
     * @param  mixed  $placeholder Single param binding or array of same
     * @param  string $operator    Operator to join multiple LIKEs
     * @return object              \rakelley\jhframe\interfaces\services\IDatabase
     */
    public function Like($column, $placeholder, $operator=null);


    /**
     * Adds a column is null to the where statement
     * 
     * @param  string $column Column name
     * @return object         \rakelley\jhframe\interfaces\services\IDatabase
     */
    public function isNull($column);


    /**
     * Adds a column is not null to the where statement
     * 
     * @param  string $column Column name
     * @return object         \rakelley\jhframe\interfaces\services\IDatabase
     */
    public function isNotNull($column);
}
