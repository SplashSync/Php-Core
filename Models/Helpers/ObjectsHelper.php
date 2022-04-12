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

namespace   Splash\Models\Helpers;

use Splash\Client\Splash;
use Splash\Models\AbstractObject;
use Splash\Models\Fields\FieldsManagerTrait;
use Splash\Models\Objects\IntelParserTrait;

/**
 * Helper for Objects Fields Management
 */
class ObjectsHelper
{
    use FieldsManagerTrait;

    /**
     * Create an Object Identifier String
     *
     * @param string $objectType Object Type Name.
     * @param string $objectId   Object Identifier
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
        return   $objectId.IDSPLIT.$objectType;
    }

    /**
     * Retrieve Identifier from an Object Identifier String
     *
     * @param string $objectId Object Identifier String.
     *
     * @return null|string
     *
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function id(string $objectId): ?string
    {
        //====================================================================//
        // Forward to Fields Manager
        return self::objectId($objectId);
    }

    /**
     * Retrieve Object Type Name from an Object Identifier String
     *
     * @param string $objectId Object Identifier String.
     *
     * @return null|string
     */
    public static function type(string $objectId): ?string
    {
        //====================================================================//
        // Forward to Fields Manager
        return   self::objectType($objectId);
    }

    /**
     * Load a Target Remote Object using Splash Object Field Data
     *
     * @param string            $fieldData   Object Identifier String.
     * @param null|class-string $objectClass
     *
     * @return null|AbstractObject
     */
    public static function load(string $fieldData, string $objectClass = null): ?AbstractObject
    {
        //====================================================================//
        // Decode Object Type & Id
        $objectType = self::objectType($fieldData);
        $objectId = self::objectId($fieldData);
        if (!$objectType || !$objectId) {
            return null;
        }
        //====================================================================//
        // Load Splash Object
        try {
            $splashObject = Splash::object($objectType);
        } catch (\Exception $e) {
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
        return   $remoteObject;
    }
}
