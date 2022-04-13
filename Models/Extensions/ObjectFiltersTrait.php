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

use Splash\Core\SplashCore as Splash;
use Splash\Models\ObjectFilterInterface;

trait ObjectFiltersTrait
{
    /**
     * List of all Objects Filters
     *
     * @var ObjectFilterInterface[]
     */
    private static $objectFilters = array();

    /**
     * Get List of All Objects Filters
     *
     * @return ObjectFilterInterface[]
     */
    public static function getObjectFilters(): array
    {
        self::loadExtensionsByPath();

        return self::$objectFilters;
    }

    /**
     * Add Object Filter
     *
     * @param ObjectFilterInterface $filter
     *
     * @return bool
     */
    public static function addObjectFilter(ObjectFilterInterface $filter): bool
    {
        self::loadExtensionsByPath();
        self::$objectFilters[get_class($filter)] = $filter;

        return true;
    }

    /**
     * Check if this Object is Filtered.
     *
     * If possible, Object is provided, but if not, only Object ID
     *
     * @param string      $objectType Object Type Name
     * @param string      $objectId   Object ID
     * @param null|object $object     Object (Optional)
     *
     * @return bool
     */
    public static function isFiltered(string $objectType, string $objectId, ?object $object = null): bool
    {
        $result = false;
        //====================================================================//
        // Walk on Registered Objects Filters
        foreach (self::getObjectFilters() as $filter) {
            try {
                //====================================================================//
                // This Filter Apply to this Object Type
                if (!in_array($objectType, $filter->getFilteredTypes(), true)) {
                    continue;
                }
                //====================================================================//
                // Write Field Value from Extensions
                $result = $result || $filter->isFiltered($objectType, $objectId, $object);
            } catch (\Throwable $ex) {
                Splash::log()->report($ex);
            }
        }

        return $result;
    }

    /**
     * Check if File as an Object Filter
     *
     * @param string $filename
     * @param string $fullPath
     *
     * @return bool
     */
    private static function registerObjectFilterFile(string $filename, string $fullPath): bool
    {
        //====================================================================//
        // Build Object Class
        $className = "Splash\\Local\\Objects\\Filters\\".$filename;
        //====================================================================//
        // Check if Class
        if (is_null($classString = self::isClassFile($className, $fullPath))) {
            return false;
        }
        //====================================================================//
        // Verify Class Implements ObjectFilterInterface
        if (is_subclass_of($classString, ObjectFilterInterface::class)) {
            try {
                return self::addObjectFilter(new $classString());
            } catch (\Throwable $ex) {
                return Splash::log()->report($ex);
            }
        }

        return false;
    }
}
