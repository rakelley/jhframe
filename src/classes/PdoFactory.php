<?php
/**
 * @package jhframe
 * @license http://opensource.org/licenses/MIT The MIT License
 * @author Ryan Kelley
 * @copyright 2011-2015 Jakked Hardcore Gym
 */

namespace rakelley\jhframe\classes;

/**
 * Factory for \PDO
 */
class PdoFactory implements \rakelley\jhframe\interfaces\IFactory
{
    use \rakelley\jhframe\traits\ConfigAware;

    /**
     * DSN for connection
     * @var string
     */
    protected $dsn;
    /**
     * Password for connection
     * @var string
     */
    protected $password;
    /**
     * Username for connection
     * @var string
     */
    protected $username;


    function __construct()
    {
        $this->dsn = $this->getConfig()->Get('SECRETS', 'DB_DSN');
        $this->username = $this->getConfig()->Get('SECRETS', 'DB_USER');
        $this->password = $this->getConfig()->Get('SECRETS', 'DB_PASS');
    }


    /**
     * @see \rakelley\jhframe\interfaces\IFactory::getProduct
     */
    public function getProduct()
    {
        if (!$this->dsn || !$this->username || !$this->password) {
            throw new \RuntimeException(
                'Cannot Create a PDO Connection, DB Credentials Not Found',
                500
            );
        }

        $pdo = new \PDO($this->dsn, $this->username, $this->password);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        return $pdo;
    }
}
