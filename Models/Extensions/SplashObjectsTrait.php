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

use Splash\Models\Objects\ObjectInterface;

/**
 * Manage Access to Additional Splash Objects Classes
 */
trait SplashObjectsTrait
{
    /**
     * List of all Extra Objects
     *
     * @var array<string, class-string>
     */
    private static $objects = array();

    /**
     * Get List of All Extra Splash Objects
     *
     * @return array<string, class-string>
     */
    public static function getObjects(): array
    {
        self::loadExtensionsByPath();

        return self::$objects;
    }

    /**
     * Check if File as a Splash Object Class
     *
     * @param string $filename
     * @param string $fullPath
     *
     * @return bool
     */
    private static function registerSplashObjectFile(string $filename, string $fullPath): bool
    {
        //====================================================================//
        // Build Object Class
        $className = "Splash\\Local\\Objects\\".$filename;
        //====================================================================//
        // Check if Class
        if (is_null($classString = self::isClassFile($className, $fullPath))) {
            return false;
        }
        //====================================================================//
        // Verify Class Implements ObjectInterface
        if (is_subclass_of($classString, ObjectInterface::class) && !$classString::isDisabled()) {
            self::$objects[$filename] = $classString;

            return true;
        }

        return false;
    }
}
