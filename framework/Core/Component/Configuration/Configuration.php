<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 21.01.18
 */

namespace Artifly\Core\Component\Configuration;


class Configuration
{
    const DEV_MODE = 'dev';
    const PROD_MODE = 'prod';
    private $mode = self::PROD_MODE;

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