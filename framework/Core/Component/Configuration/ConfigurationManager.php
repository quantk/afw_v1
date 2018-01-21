<?php
/**
 * Created by PhpStorm.
 * User: quantick
 * Date: 21.01.18
 */

namespace Artifly\Core\Component\Configuration;


use Artifly\Core\Component\Configuration\Exception\ConfigurationError;
use Symfony\Component\Yaml\Yaml;

class ConfigurationManager
{
//region SECTION: Fields
    /**
     * @var Configuration
     */
    private $configuration = null;
//endregion Fields

//region SECTION: Constructor
    /**
     * ConfigurationManager constructor.
     */
    public function __construct()
    {
    }
//endregion Constructor

//region SECTION: Public
    /**
     * @param $configPath
     *
     * @throws ConfigurationError
     */
    public function parse($configPath)
    {
        $parsed = Yaml::parse(file_get_contents($configPath));
        if (!isset($parsed['configuration'])) {
            throw new ConfigurationError('No configuration section in config file');
        }
        $config = $parsed['configuration'];
        $mode   = $config['mode'] ?? Configuration::PROD_MODE;
        $appname = $config['appname'] ?? 'Application';

        $configuration = new Configuration();
        $configuration->setMode($mode);
        $configuration->setAppName($appname);

        $this->configuration = $configuration;
    }
//endregion Public

//region SECTION: Getters/Setters
    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param Configuration $configuration
     *
     * @return ConfigurationManager
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }
//endregion Getters/Setters
}