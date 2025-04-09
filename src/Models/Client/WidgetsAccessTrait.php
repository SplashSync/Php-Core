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

namespace Splash\Core\Models\Client;

use Exception;
use Splash\Core\Components\FilesLoader;
use Splash\Core\Dictionary\SplDefinition;
use Splash\Core\Interfaces\Local\WidgetsProviderInterface;
use Splash\Core\Models\Widgets\WidgetInterface;

/**
 * Core Functions for Access to Splash Widgets
 */
trait WidgetsAccessTrait
{
    /**
     * Splash Widgets Class Buffer
     *
     * @var array<string, WidgetInterface>
     */
    protected array $widgets = array();

    /**
     * Get Specific Widget Class
     *
     * This function is a router for all local widgets classes & functions
     *
     * @param string $widgetType Local Widget Class Name
     *
     * @throws Exception
     */
    public static function widget(string $widgetType): WidgetInterface
    {
        //====================================================================//
        // Check in Cache
        if (array_key_exists($widgetType, self::core()->widgets)) {
            return self::core()->widgets[$widgetType];
        }
        //====================================================================//
        // Verify if Widget Class is Valid
        if (!self::validate()->isValidWidget($widgetType)) {
            throw new Exception('You requested access to an Invalid Widget Type : '.$widgetType);
        }
        //====================================================================//
        // Check if Widget Manager is Override
        $local = self::local();
        if ($local instanceof WidgetsProviderInterface) {
            //====================================================================//
            // Initialize Local Widget Manager
            self::core()->widgets[$widgetType] = $local->widget($widgetType);
        } else {
            //====================================================================//
            // Initialize Class
            $className = SplDefinition::WIDGETS_PREFIX.$widgetType;
            if (!class_exists($className) || !is_subclass_of($className, WidgetInterface::class)) {
                throw new Exception('Invalid Widget Class : '.$className);
            }
            self::core()->widgets[$widgetType] = new $className();
        }

        //====================================================================//
        // Load Translation File
        self::translator()->load('widgets');

        return self::core()->widgets[$widgetType];
    }

    /**
     * Build list of Available Widgets
     *
     * @throws Exception
     *
     * @return string[]
     */
    public static function widgets(): array
    {
        //====================================================================//
        // Check if Widget Manager has Overrides
        $local = self::local();
        if ($local instanceof WidgetsProviderInterface) {
            return $local->widgets();
        }
        $widgetTypes = array();
        //====================================================================//
        // Load Objects from Local Objects Path
        $files = FilesLoader::load(self::getLocalPath().'/Widgets', 'php', 0);
        foreach (array_keys($files) as $className) {
            //====================================================================//
            // Verify ClassName is a Valid Object File
            if (!self::validate()->isValidWidget($className)) {
                continue;
            }
            $widgetTypes[] = $className;
        }

        return $widgetTypes;
    }
}
