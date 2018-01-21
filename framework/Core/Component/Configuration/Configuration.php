<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 21.01.18
 */

namespace Artifly\Core\Component\Configuration;


/**
 * Class Configuration
 *
 * @package Artifly\Core\Component\Configuration
 */
class Configuration
{
    const DEV_MODE = 'dev';
    const PROD_MODE = 'prod';
    /**
     * @var string
     */
    private $mode = self::PROD_MODE;
    /**
     * @var string
     */
    private $appName = '';

    /**
     * @return string
     */
    public function getAppName()
    {
        return $this->appName;
    }

    /**
     * @param string $appName
     *
     * @return Configuration
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;

        return $this;
    }

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * @param string $mode
     *
     * @return Configuration
     */
    public function setMode($mode)
    {
        $this->mode = $mode;

        return $this;
    }
}