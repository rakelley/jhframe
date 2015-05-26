<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\interfaces\services;

/**
 * Interface for service which handles creation and use of \PDOStatement
 */
interface IStatementAbstractor
{

    /**
     * Create a new PDOStatement by preparing the query
     * 
     * @param  string $query
     * @return object        $this
     */
    public function makeStatement($query);


    /**
     * Return the prepared statement
     * 
     * @return object \PDOStatement
     */
    public function returnStatement();


    /**
     * Binding corresponding value (or null if no value provided) to prepared
     * statement for key(s).
     * 
     * @param  mixed $key    String placeholder to bind value for, or array of
     *                       same
     * @param  array $values Values to bind for key(s)
     * @return object        $this
     */
    public function Bind($key, array $values);


    /**
     * Alias of PDOStatement->execute($values)
     * 
     * @param  array $values Values to pass to execute
     * @return object        $this
     */
    public function Execute(array $values=null);


    /**
     * Alias of PDOStatement->execute($values); return PDOStatement->fetch();
     * 
     * @param  array $values Values to pass to execute
     * @return mixed         Result of fetch
     */
    public function Fetch(array $values=null);


    /**
     * Alias of PDOStatement->execute($values); return PDOStatement->fetchAll();
     * 
     * @param  array $values Values to pass to execute
     * @return array         Result of fetchAll
     */
    public function FetchAll(array $values=null);
}
