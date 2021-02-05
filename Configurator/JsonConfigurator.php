<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Configurator;

use Splash\Core\SplashCore as Splash;
use Splash\Models\AbstractConfigurator;
use Splash\Models\ConfiguratorInterface;

/**
 * Use Json Configurator to Load Configuration from a Local Json File
 */
class JsonConfigurator extends AbstractConfigurator implements ConfiguratorInterface
{
    /**
     * Custom Configuration Array
     *
     * @var array
     */
    private static $configuration;

    //====================================================================//
    // ACCESS TO LOCAL CONFIGURATION
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        if (!isset(static::$configuration)) {
            //====================================================================//
            //  Check if Custom Configuration Path is Defined
            $cfgPath = $this->getConfigPath();
            if (false == $cfgPath) {
                return static::$configuration = array();
            }
            //====================================================================//
            //  Check if Custom Configuration is Empty
            $config = $this->getConfigArray($cfgPath);
            if (!is_array($config) || empty($config)) {
                return static::$configuration = array();
            }

            static::$configuration = $config;
        }

        return static::$configuration;
    }

    /**
     * Get Json Configuration File Path
     *
     * @return false|string False or Configuration File Path
     */
    private function getConfigPath()
    {
        //====================================================================//
        //  Load Module Configuration
        $cfg = Splash::configuration();
        //====================================================================//
        //  Check if Custom Configuration Path is Defined
        $cfgPath = Splash::getLocalPath()."/configuration.json";
        if (isset($cfg->ConfiguratorPath) && is_string($cfg->ConfiguratorPath)) {
            $cfgPath = $cfg->ConfiguratorPath;
        }
        //====================================================================//
        //  Check if Custom Configuration File Exists
        Splash::log()->deb("Try Loading Custom Configuration From: ".$cfgPath);
        if (!is_file($cfgPath)) {
            return false;
        }

        return $cfgPath;
    }

    /**
     * Load Configuration File
     *
     * @param string $cfgPath Configuration File Path
     *
     * @return array|false False or Configuration Array
     */
    private function getConfigArray($cfgPath)
    {
        //====================================================================//
        //  Check if Custom Configuration File Exists
        if (!is_file($cfgPath)) {
            return false;
        }
        //====================================================================//
        //  Load File Contents
        $rawJson = file_get_contents($cfgPath);
        if (!is_string($rawJson)) {
            return false;
        }
        //====================================================================//
        //  Decode Json Contents
        $json = json_decode($rawJson, true);
        if (!is_array($json)) {
            Splash::log()->war("Invalid Json Configuration. Overrides Skipped!");

            return false;
        }
        Splash::log()->deb("Loaded Custom Configuration From: ".$cfgPath);

        return $json;
    }
}
