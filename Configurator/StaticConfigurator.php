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

use Splash\Models\AbstractConfigurator;
use Splash\Models\ConfiguratorInterface;

/**
 * Static Configurator to Load Configuration from a Class
 */
class StaticConfigurator extends AbstractConfigurator implements ConfiguratorInterface
{
    /**
     * Custom Configuration Array
     *
     * @var array
     */
    protected static array $configuration = array();

    //====================================================================//
    // SETUP CONFIGURATION FOR AN OBJECT
    //====================================================================//

    /**
     * Setup Configuration for an Object
     *
     * @param string $objectType
     * @param array  $objectConfig
     *
     * @return void
     */
    public function setObjectConfiguration(string $objectType, array $objectConfig): void
    {
        static::$configuration[$objectType] = $objectConfig;
    }

    //====================================================================//
    // ACCESS TO LOCAL CONFIGURATION
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        return static::$configuration;
    }
}
