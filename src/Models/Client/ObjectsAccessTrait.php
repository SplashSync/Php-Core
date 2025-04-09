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
use Splash\Core\Components\ExtensionsManager;
use Splash\Core\Components\FilesLoader;
use Splash\Core\Dictionary\SplDefinition;
use Splash\Core\Interfaces\Local\ObjectsProviderInterface;
use Splash\Core\Interfaces\Object\ObjectInterface;

/**
 * Core Functions for Access to Splash Objects
 */
trait ObjectsAccessTrait
{
    /**
     * Splash Objects Class Buffer
     *
     * @var array<string, ObjectInterface>
     */
    protected array $objects = array();

    /**
     * Get Specific Object Class
     *
     * This function is a router for all local object classes & functions
     *
     * @param string $objectType Local Object Class Name
     *
     * @throws Exception
     */
    public static function object(string $objectType): ObjectInterface
    {
        //====================================================================//
        // Check in Cache
        if (array_key_exists($objectType, self::core()->objects)) {
            return self::core()->objects[$objectType];
        }
        //====================================================================//
        // Verify if Object Class is Valid
        if (!self::validate()->isValidObject($objectType)) {
            throw new Exception('You requested access to an Invalid Object Type : '.$objectType);
        }

        //====================================================================//
        // Check if Object Manager has Override
        $local = self::local();
        if ($local instanceof ObjectsProviderInterface) {
            //====================================================================//
            // Initialize Local Object Manager
            self::core()->objects[$objectType] = $local->object($objectType);
        } else {
            //====================================================================//
            // Initialize Standard Class
            $className = SplDefinition::OBJECTS_PREFIX.$objectType;
            if (!class_exists($className) || !is_subclass_of($className, ObjectInterface::class)) {
                throw new Exception('Invalid Object Class : '.$className);
            }
            self::core()->objects[$objectType] = new $className();
        }

        //====================================================================//
        // Load Translation File
        self::translator()->load('objects');

        return self::core()->objects[$objectType];
    }

    /**
     * Build list of Available Objects
     *
     * @throws Exception
     *
     * @return string[]
     */
    public static function objects(): array
    {
        //====================================================================//
        // Check if Object Manager has Overrides
        $local = self::local();
        if ($local instanceof ObjectsProviderInterface) {
            return $local->objects();
        }
        $objectsList = array();
        //====================================================================//
        // Load Objects from Local Objects Path
        $files = FilesLoader::load(self::getLocalPath().'/Objects', 'php', 0);
        foreach (array_keys($files) as $className) {
            //====================================================================//
            // Verify ClassName is a Valid Object File
            if (!self::validate()->isValidObject($className)) {
                continue;
            }
            $objectsList[] = $className;
        }

        //====================================================================//
        // Load Objects from Extensions
        return array_merge($objectsList, array_keys(ExtensionsManager::getObjects()));
    }
}
