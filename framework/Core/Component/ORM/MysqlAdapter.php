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
     * @param string $hostname
     * @param        $dbname
     * @param string $user
     * @param string $pass
     * @param string $charset
     */
    public function __construct($hostname = 'localhost', $dbname, $user = 'root', $pass = '', $charset = self::DEFAULT_CHARSET)
    {
        $this->hostname   = $hostname;
        $this->DBName     = $dbname;
        $this->user       = $user;
        $this->pass       = $pass;
        $this->charset    = $charset;
        $this->connString = sprintf('mysql:host=%s;dbname=%s;charset=%s', $this->hostname, $this->DBName, $charset);
    }
//endregion Constructor
}