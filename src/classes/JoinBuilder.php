<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

use \rakelley\jhframe\interfaces\services\IDatabase;

/**
 * Default implementation for IDatabaseJoinBuilder
 */
class JoinBuilder implements
    \rakelley\jhframe\interfaces\services\IDatabaseJoinBuilder
{
    /**
     * IDatabase instance
     * @var object
     */
    private $db = null;
    /**
     * Current join statement
     * @var string
     */
    private $join = '';


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IDatabaseJoinBuilder::newJoin()
     */
    public function newJoin(IDatabase $db, $table, $type=null)
    {
        $this->db = $db;
        $type = (isset($type)) ? ' ' . $type : '';

        $this->join = "$type JOIN `$table`";
        return $this;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IDatabaseJoinBuilder::On()
     */
    public function On($local, $foreign)
    {
        $this->join .= " ON `$local` = `$foreign`";

        $this->db->Append($this->join);
        $this->join = '';
        return $this->db;
    }


    /**
     * {@inheritdoc}
     * @see \rakelley\jhframe\interfaces\services\IDatabaseJoinBuilder::Using()
     */
    public function Using($column)
    {
        $this->join .= " USING (`$column`)";

        $this->db->Append($this->join);
        $this->join = '';
        return $this->db;
    }
}
