<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Models\Helpers;

use Splash\Models\Fields\FieldsManagerTrait;

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
}
