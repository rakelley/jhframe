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

namespace rakelley\jhframe\interfaces\services;

/**
 * Handles the creation of JOIN statements within a query being constructed by
 * an IDatabase
 */
interface IDatabaseJoinBuilder
{

    /**
     * Initiates new Join
     * 
     * @param  object $db    \rakelley\jhframe\interfaces\services\IDatabase
     * @param  string $table Database table to join
     * @param  string $type  Type of join
     * @return object        $this
     */
    public function newJoin(IDatabase $db, $table, $type=null);


    /**
     * Adds an ON statement to the join and appends to db query
     * 
     * @param  string $first  First column name
     * @param  string $second Second column name
     * @return object         \rakelley\jhframe\interfaces\services\IDatabase
     */
    public function On($first, $second);


    /**
     * Adds a USING statement to the join and appends to db query
     * 
     * @param  string $column Column name
     * @return object         \rakelley\jhframe\interfaces\services\IDatabase
     */
    public function Using($column);
}
