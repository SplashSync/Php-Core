<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Configurator;

use Exception;
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
    private static array $configuration;

    //====================================================================//
    // ACCESS TO LOCAL CONFIGURATION
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        if (!isset(self::$configuration)) {
            //====================================================================//
            //  Check if Custom Configuration Path is Defined
            $cfgPath = $this->getConfigPath();
            if (!$cfgPath) {
                return self::$configuration = array();
            }
            //====================================================================//
            //  Check if Custom Configuration is Empty
            $config = $this->getConfigArray($cfgPath);
            if (!is_array($config) || empty($config)) {
                return self::$configuration = array();
            }

            self::$configuration = $config;
        }

        return self::$configuration;
    }

    /**
     * Get Json Configuration File Path
     *
     * @return null|string
     */
    private function getConfigPath(): ?string
    {
        //====================================================================//
        //  Load Module Configuration
        $cfg = Splash::configuration();
        //====================================================================//
        //  Check if Custom Configuration Path is Defined
        try {
            $cfgPath = Splash::getLocalPath()."/configuration.json";
        } catch (Exception $e) {
            return null;
        }
        if (isset($cfg->ConfiguratorPath) && is_string($cfg->ConfiguratorPath)) {
            $cfgPath = $cfg->ConfiguratorPath;
        }
        //====================================================================//
        //  Check if Custom Configuration File Exists
        Splash::log()->deb("Try Loading Custom Configuration From: ".$cfgPath);
        if (!is_file($cfgPath)) {
            return null;
        }

        return $cfgPath;
    }

    /**
     * Load Configuration File
     *
     * @param string $cfgPath Configuration File Path
     *
     * @return null|array
     */
    private function getConfigArray(string $cfgPath): ?array
    {
        //====================================================================//
        //  Check if Custom Configuration File Exists
        if (!is_file($cfgPath)) {
            return null;
        }
        //====================================================================//
        //  Load File Contents
        $rawJson = file_get_contents($cfgPath);
        if (!is_string($rawJson)) {
            return null;
        }
        //====================================================================//
        //  Decode Json Contents
        $json = json_decode($rawJson, true);
        if (!is_array($json)) {
            Splash::log()->war("Invalid Json Configuration. Overrides Skipped!");

            return null;
        }
        Splash::log()->deb("Loaded Custom Configuration From: ".$cfgPath);

        return $json;
    }
}
