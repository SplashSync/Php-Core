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

namespace Splash\Core\Helpers;

use Exception;
use Splash\Core\Client\Splash;
use Splash\Core\Models\AbstractObject;
use Splash\Core\Models\Objects\IntelParserTrait;

/**
 * Helper for Objects Fields Management
 */
class ObjectsHelper
{
    /**
     * Object Id Splitter
     */
    const SPLIT = '::';

    /**
     * Object ID Prop
     */
    const ID = 'ObjectId';

    /**
     * Object Type Prop
     */
    const TYPE = 'ObjectType';

    /**
     * Create an Object Identifier String
     *
     * @param string $objectType Object Type Name.
     * @param string $objectId   Object Identifier
     *
     * @phpstan-return ($objectType is non-empty-string ? ($objectId is non-empty-string ? string : null) : null)
     *
     * @return null|string
     */
    public static function encode(string $objectType, string $objectId): ?string
    {
        //====================================================================//
        // Safety Checks
        if (empty($objectType)) {
            return null;
        }
        if (empty($objectId)) {
            return null;
        }

        //====================================================================//
        // Create & Return Field Id Data String
        return   $objectId.self::SPLIT.$objectType;
    }

    /**
     * Check if a Field ID or Type is an Object Identifier
     *
     * @param null|string $fieldType Data Type Name String
     *
     * @return bool
     */
    public static function isIdField(?string $fieldType): bool
    {
        return is_array(self::explode($fieldType));
    }

    /**
     * Retrieve Identifier from an Object Identifier String
     *
     * @param string $fieldId Object Identifier String.
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function id(string $fieldId): ?string
    {
        return self::explode($fieldId)[self::ID] ?? null;
    }

    /**
     * Retrieve Object Type Name from an Object Identifier String
     *
     * @param string $fieldId Object Identifier String.
     */
    public static function type(string $fieldId): ?string
    {
        return self::explode($fieldId)[self::TYPE] ?? null;
    }

    /**
     * Load a Target Remote Object using Splash Object Field Data
     *
     * @param string            $fieldData   Object Identifier String.
     * @param null|class-string $objectClass
     *
     * @return null|AbstractObject
     */
    public static function load(string $fieldData, ?string $objectClass = null): ?AbstractObject
    {
        //====================================================================//
        // Decode Object Type & Id
        $objectId = self::id($fieldData);
        $objectType = self::type($fieldData);
        if (!$objectType || !$objectId) {
            return null;
        }

        //====================================================================//
        // Load Splash Object
        try {
            $splashObject = Splash::object($objectType);
        } catch (Exception $e) {
            return null;
        }
        //====================================================================//
        // Ensure Splash Object uses IntelParserTrait
        if (!in_array(IntelParserTrait::class, (array) class_uses($splashObject), true)) {
            return null;
        }
        if (!method_exists($splashObject, 'load')) {
            return null;
        }
        //====================================================================//
        // Load Remote Object
        $remoteObject = $splashObject->load($objectId);
        if (!$remoteObject) {
            return null;
        }
        //====================================================================//
        // Verify Remote Object
        if (!empty($objectClass) && !($remoteObject instanceof $objectClass)) {
            return null;
        }

        //====================================================================//
        // Return Remote Object
        return $remoteObject;
    }

    /**
     * Identify if field is Object Identifier Data & Decode Field
     *
     * @param null|string $fieldId ObjectId Field String
     *
     * @return null|array
     */
    private static function explode(?string $fieldId): ?array
    {
        //====================================================================//
        // Safety Check
        if (empty($fieldId)) {
            return null;
        }
        //====================================================================//
        // Detects ObjectId
        $list = explode(self::SPLIT, $fieldId);
        if (is_array($list) && (2 == count($list))) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            $result[self::ID] = $list[0];
            $result[self::TYPE] = $list[1];

            return $result;
        }

        return null;
    }
}
