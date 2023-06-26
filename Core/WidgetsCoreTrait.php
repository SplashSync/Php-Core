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

namespace   Splash\Core;

use Exception;
use Splash\Models\Widgets\WidgetInterface;
use Splash\Models\WidgetsProviderInterface;

//====================================================================//
//********************************************************************//
//====================================================================//
//  SPLASH REMOTE FRAMEWORK CORE CLASS
//====================================================================//
//********************************************************************//
//====================================================================//

/**
 * Core Functions for Access to Splash Widgets
 */
trait WidgetsCoreTrait
{
    /**
     * Splash Widgets Class Buffer
     *
     * @var array<string, WidgetInterface>
     */
    protected array $widgets = array();

    /**
     * Get Specific Widget Class
     * This function is a router for all local widgets classes & functions
     *
     * @param string $widgetType Local Widget Class Name
     *
     * @throws Exception
     *
     * @return WidgetInterface
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
        if (self::local() instanceof WidgetsProviderInterface) {
            //====================================================================//
            // Initialize Local Widget Manager
            self::core()->widgets[$widgetType] = self::local()->widget($widgetType);
        } else {
            //====================================================================//
            // Initialize Class
            $className = SPLASH_CLASS_PREFIX.'\\Widgets\\'.$widgetType;
            if (!class_exists($className) || !is_subclass_of($className, WidgetInterface::class)) {
                throw new Exception('Invalid Widget Class : '.$className);
            }
            self::core()->widgets[$widgetType] = new $className();
        }

        //====================================================================//
        //  Load Translation File
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
        if (self::local() instanceof WidgetsProviderInterface) {
            return self::local()->widgets();
        }
        $widgetTypes = array();
        //====================================================================//
        // Safety Check => Verify Objects Folder Exists
        $path = self::getLocalPath().'/Widgets';
        if (!is_dir($path)) {
            return $widgetTypes;
        }
        //====================================================================//
        // Scan Local Objects Folder
        $scan = scandir($path, 1);
        if (false == $scan) {
            return $widgetTypes;
        }
        //====================================================================//
        // Scan Each File in Folder
        $files = array_diff($scan, array('..', '.', 'index.php', 'index.html'));
        foreach ($files as $filename) {
            $className = pathinfo($path.'/'.$filename, PATHINFO_FILENAME);
            //====================================================================//
            // Verify ClassName is a Valid Object File
            if (false == self::validate()->isValidWidget($className)) {
                continue;
            }
            $widgetTypes[] = $className;
        }

        return $widgetTypes;
    }
}
