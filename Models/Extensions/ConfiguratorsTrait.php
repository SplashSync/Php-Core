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

namespace Splash\Models\Extensions;

use Splash\Components\FieldsFactory;
use Splash\Core\SplashCore as Splash;
use Splash\Models\AbstractConfigurator;

/**
 * Manage Loading & Execution of Splash Extended Configurators
 */
trait ConfiguratorsTrait
{
    /**
     * List of all Configurators
     *
     * @var AbstractConfigurator[]
     */
    private static $configurators = array();

    /**
     * Get List of All Configurators
     *
     * @return AbstractConfigurator[]
     */
    public static function getConfigurators(): array
    {
        self::loadExtensionsByPath();

        return self::$configurators;
    }

    /**
     * Add a Configurators
     *
     * @param AbstractConfigurator $configurator
     *
     * @return bool
     */
    public static function addConfigurator(AbstractConfigurator $configurator): bool
    {
        self::loadExtensionsByPath();
        self::$configurators[basename(get_class($configurator))] = $configurator;

        return true;
    }

    /**
     * Register Extension Configurators
     *
     * @param string        $objectType
     * @param FieldsFactory $factory
     *
     * @return void
     */
    public static function registerConfigurators(string $objectType, FieldsFactory $factory): void
    {
        foreach (self::getConfigurators() as $configurator) {
            $factory->registerConfigurator($objectType, $configurator);
        }
    }

    /**
     * Check if File as an Object Filter
     *
     * @param string $filename
     * @param string $fullPath
     *
     * @return bool
     */
    protected static function registerConfiguratorFile(string $filename, string $fullPath): bool
    {
        //====================================================================//
        // Build Object Class
        $className = "Splash\\Local\\Configurators\\".$filename;
        //====================================================================//
        // Check if Class
        if (is_null($classString = self::isClassFile($className, $fullPath))) {
            return false;
        }
        //====================================================================//
        // Verify Class Implements ObjectFilterInterface
        if (is_subclass_of($classString, AbstractConfigurator::class)) {
            try {
                return self::addConfigurator(new $classString());
            } catch (\Throwable $ex) {
                return Splash::log()->report($ex);
            }
        }

        return false;
    }
}
