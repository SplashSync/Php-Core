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
use Splash\Models\ObjectExtensionInterface;

/**
 * Manage Loading & Access to Splash Objects Extensions
 */
trait ObjectExtensionsTrait
{
    /**
     * List of all Objects Extensions
     *
     * @var ObjectExtensionInterface[]
     */
    private static $objectExtensions = array();

    /**
     * Get List of All Objects Extensions
     *
     * @return ObjectExtensionInterface[]
     */
    public static function getObjectExtensions(): array
    {
        self::loadExtensionsByPath();

        return self::$objectExtensions;
    }

    /**
     * Add an Object Extension
     *
     * @param ObjectExtensionInterface $extension
     *
     * @return bool
     */
    public static function addObjectExtension(ObjectExtensionInterface $extension): bool
    {
        self::loadExtensionsByPath();
        self::$objectExtensions[get_class($extension)] = $extension;

        return true;
    }

    /**
     * Build Extensions Fields
     *
     * @param string        $objectType Object Type Name
     * @param FieldsFactory $factory    Splash Fields Factory
     *
     * @return void
     */
    public static function buildObjectExtensionsFields(string $objectType, FieldsFactory $factory): void
    {
        //====================================================================//
        // Walk on Registered Extensions
        foreach (self::getObjectExtensions() as $extension) {
            try {
                //====================================================================//
                // This Extensions Apply to this Object Type
                if (!in_array($objectType, $extension->getExtendedTypes(), true)) {
                    continue;
                }
                //====================================================================//
                // Execute Extension Field Builder
                $extension->buildExtendedFields($objectType, $factory);
            } catch (\Throwable $ex) {
                Splash::log()->report($ex);
            }
        }
    }

    /**
     * Read Object Data from Extensions
     *
     * @param string $objectType Object Type Name
     * @param object $object     Current Object
     * @param string $fieldId    ID of Field to Read
     * @param mixed  $fieldData  Data of Field to Read
     *
     * @return null|bool
     */
    public static function getObjectExtensionsFields(
        string $objectType,
        object $object,
        string $fieldId,
        &$fieldData
    ): ?bool {
        //====================================================================//
        // Walk on Registered Extensions
        foreach (self::getObjectExtensions() as $extension) {
            try {
                //====================================================================//
                // This Extensions Apply to this Object Type
                if (!in_array($objectType, $extension->getExtendedTypes(), true)) {
                    continue;
                }
                //====================================================================//
                // Read Field Value from Extensions
                $result = $extension->getExtendedFields($object, $fieldId, $fieldData);
                //====================================================================//
                // Field was Managed by Extensions
                if (null !== $result) {
                    return $result;
                }
            } catch (\Throwable $ex) {
                Splash::log()->report($ex);
            }
        }

        return null;
    }

    /**
     * Write Object Data from Extensions
     *
     * @param string $objectType Object Type Name
     * @param object $object     Current Object
     * @param string $fieldId    ID of Field to Read
     * @param mixed  $fieldData  Data of Field to Read
     *
     * @return null|bool
     */
    public static function setObjectExtensionsFields(
        string $objectType,
        object $object,
        string $fieldId,
        $fieldData
    ): ?bool {
        //====================================================================//
        // Walk on Registered Extensions
        foreach (self::getObjectExtensions() as $extension) {
            try {
                //====================================================================//
                // This Extensions Apply to this Object Type
                if (!in_array($objectType, $extension->getExtendedTypes(), true)) {
                    continue;
                }
                //====================================================================//
                // Write Field Value from Extensions
                $result = $extension->setExtendedFields($object, $fieldId, $fieldData);
                //====================================================================//
                // Field was Managed by Extensions
                if (null !== $result) {
                    return $result;
                }
            } catch (\Throwable $ex) {
                Splash::log()->report($ex);
            }
        }

        return null;
    }

    /**
     * Check if File as an Object Extension
     *
     * @param string $filename
     * @param string $fullPath
     *
     * @return bool
     */
    protected static function registerObjectExtensionFile(string $filename, string $fullPath): bool
    {
        //====================================================================//
        // Build Object Class
        $className = "Splash\\Local\\Objects\\Extensions\\".$filename;
        //====================================================================//
        // Check if Class
        if (is_null($classString = self::isClassFile($className, $fullPath))) {
            return false;
        }
        //====================================================================//
        // Verify Class Implements ObjectExtensionInterface
        if (is_subclass_of($classString, ObjectExtensionInterface::class)) {
            try {
                return self::addObjectExtension(new $classString());
            } catch (\Throwable $ex) {
                return Splash::log()->report($ex);
            }
        }

        return false;
    }
}
