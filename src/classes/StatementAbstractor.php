<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Default implementation of IStatementAbstractor
 */
class StatementAbstractor implements
    \rakelley\jhframe\interfaces\services\IStatementAbstractor
{
    /**
     * Database connection object
     * @var object
     */
    protected $connection;
    /**
     * Current prepared statement
     * @var object
     */
    protected $stmnt = null;


    function __construct(
        \PDO $connection
    ) {
        $this->connection = $connection;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IStatementAbstractor::makeStatement
     */
    public function makeStatement($query)
    {
        $this->stmnt = $this->connection->prepare($query);

        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IStatementAbstractor::returnStatement
     */
    public function returnStatement()
    {
        return $this->stmnt;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IStatementAbstractor::Bind
     */
    public function Bind($key, array $values)
    {
        if (is_array($key)) {
            array_walk(
                $key,
                function($k) use ($values) {
                    $this->Bind($k, $values);
                }
            );
        } else {
            if (!isset($values[$key])) {
                $this->stmnt->bindValue(":$key", null, \PDO::PARAM_INT);
            } elseif (is_int($values[$key])) {
                $this->stmnt->bindValue(":$key", $values[$key], \PDO::PARAM_INT);
            } else {
                $this->stmnt->bindValue(":$key", $values[$key], \PDO::PARAM_STR);
            }
        }

        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IStatementAbstractor::Execute
     */
    public function Execute(array $values=null)
    {
        $this->stmnt->execute($values);

        return $this;
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IStatementAbstractor::Fetch
     */
    public function Fetch(array $values=null)
    {
        $this->Execute($values);

        return $this->stmnt->fetch();
    }


    /**
     * @see \rakelley\jhframe\interfaces\services\IStatementAbstractor::FetchAll
     */
    public function FetchAll(array $values=null)
    {
        $this->Execute($values);

        return $this->stmnt->fetchAll();
    }
}
