<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 20.01.18
 */

namespace Artifly\Core\Component\ORM;


/**
 * Class AbstractAdapter
 *
 * @package Artifly\Core\Component\ORM
 */
abstract class AbstractAdapter
{
//region SECTION: Fields
    /**
     * @var string
     */
    protected $hostname;
    /**
     * @var string
     */
    protected $dbname;
    /**
     * @var string
     */
    protected $user;
    /**
     * @var string
     */
    protected $pass;
    /**
     * @var DBConnector
     */
    protected $connector;
    /**
     * @var string
     */
    protected $connString;
//endregion Fields

//region SECTION: Getters/Setters
    /**
     * @return string
     */
    public function getConnString()
    {
        return $this->connString;
    }

    /**
     * @return mixed
     */
    public function getDbname()
    {
        return $this->dbname;
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * @return string
     */
    public function getPass()
    {
        return $this->pass;
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }
//endregion Getters/Setters
}