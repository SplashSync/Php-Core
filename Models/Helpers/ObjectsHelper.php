<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
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
     * @return false|string
     */
    public static function encode($objectType, $objectId)
    {
        //====================================================================//
        // Safety Checks
        if (empty($objectType)) {
            return false;
        }
        if (empty($objectId)) {
            return false;
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
     * @return false|string
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function id($objectId)
    {
        //====================================================================//
        // Forward to Fields Manager
        return   self::objectId($objectId);
    }

    /**
     * Retrieve Object Type Name from an Object Identifier String
     *
     * @param string $objectId Object Identifier String.
     *
     * @return false|string
     */
    public static function type($objectId)
    {
        //====================================================================//
        // Forward to Fields Manager
        return   self::objectType($objectId);
    }

    /**
     * Load a Target Remote Object using Splash Object Field Data
     *
     * @param string     $fieldData   Object Identifier String.
     * @param null|mixed $objectClass
     *
     * @return null|AbstractObject
     */
    public static function load($fieldData, $objectClass = null)
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
        $splashObject = Splash::object($objectType);
        if (empty($splashObject)) {
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
