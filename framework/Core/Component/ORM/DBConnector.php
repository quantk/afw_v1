<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 20.01.18
 */

namespace Artifly\Core\Component\ORM;


/**
 * Class DBConnector
 *
 * @package Artifly\Core\Component\ORM
 */
class DBConnector
{
//region SECTION: Fields
    /**
     * @var array
     */
    protected $errors;
    /**
     * @var \PDO
     */
    private $pdo;
    /**
     * @var AbstractAdapter
     */
    private $adapter;
//endregion Fields

//region SECTION: Constructor
    /**
     * DBConnector constructor.
     *
     * @param AbstractAdapter $adapter
     */
    public function __construct(AbstractAdapter $adapter)
    {
        $this->adapter = $adapter;
    }
//endregion Constructor

//region SECTION: Protected
    /**
     * @param     $message
     * @param int $code
     */
    protected function addError($message, $code = 0)
    {
        $this->errors[$code] = $message;
    }
//endregion Protected

//region SECTION: Public
    /**
     * @return bool
     */
    public function isConnected()
    {
        return $this->pdo instanceof \PDO && empty($this->errors);
    }

    /**
     * Close connection
     */
    public function __destruct()
    {
        $this->pdo = null;
    }

    /**
     * Connection
     */
    public function connect()
    {
        try {
            $this->pdo = new \PDO(
                $this->adapter->getConnString(),
                $this->adapter->getUser(),
                $this->adapter->getPass(),
                [
                    \PDO::ATTR_PERSISTENT => true,
                ]
            );
            $this->pdo->setAttribute(
                \PDO::ATTR_ERRMODE,
                \PDO::ERRMODE_EXCEPTION
            );
        } catch (\PDOException $e) {
            $this->addError($e->getMessage(), $e->getCode());
        }
    }
//endregion Public

//region SECTION: Getters/Setters
    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return AbstractAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }
//endregion Getters/Setters
}