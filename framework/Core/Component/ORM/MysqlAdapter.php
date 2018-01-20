<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 20.01.18
 */

namespace Artifly\Core\Component\ORM;


/**
 * Class MysqlAdapter
 *
 * @package Artifly\Core\Component\ORM
 */
class MysqlAdapter extends AbstractAdapter
{
//region SECTION: Constructor
    /**
     * MysqlAdapter constructor.
     *
     * @param $hostname
     * @param $dbname
     * @param $user
     * @param $pass
     */
    public function __construct($hostname = 'localhost', $dbname, $user = 'root', $pass = '')
    {
        $this->hostname   = $hostname;
        $this->dbname     = $dbname;
        $this->user       = $user;
        $this->pass       = $pass;
        $this->connString = sprintf('mysql:host=%s;dbname=%s;charset=UTF8', $this->hostname, $this->dbname);
    }
//endregion Constructor
}