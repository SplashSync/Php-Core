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

use ArrayObject;
use stdClass;

/**
 * Helper for Inline Fields Management
 */
class InlineHelper
{
    /**
     * Encode Inline Field String from Array
     *
     * @param null|array|ArrayObject $data Field Data.
     *
     * @return string
     */
    public static function fromArray($data): string
    {
        //====================================================================//
        // Empty Value
        if (empty($data)) {
            return "[]";
        }
        //====================================================================//
        // Detect ArrayObjects
        if (($data instanceof ArrayObject)) {
            $data = $data->getArrayCopy();
        }
        //====================================================================//
        // Create & Return Field Data as Json String
        return  (string) json_encode(array_values($data), JSON_UNESCAPED_UNICODE, 1);
    }

    /**
     * Encode Inline Field String
     *
     * @param null|stdClass $data Field Data.
     *
     * @return string
     */
    public static function fromStdClass(?stdClass $data): string
    {
        //====================================================================//
        // Empty Value
        if (empty($data)) {
            return "[]";
        }
        //====================================================================//
        // Create & Return Field Data as Json String
        return self::fromArray((array) $data);
    }

    /**
     * Decode Inline Field String to Array
     *
     * @param null|string $data Field Data.
     *
     * @return array
     */
    public static function toArray($data): array
    {
        //====================================================================//
        // Empty Value
        if (empty($data) || self::isEmpty($data)) {
            return array();
        }
        //====================================================================//
        // Create & Return Field Data as Json String
        $decoded = json_decode($data, true);
        if (!is_array($decoded)) {
            return array();
        }

        return $decoded;
    }

    /**
     * Decode Inline Field String to Object
     *
     * @param null|string $data Field Data.
     *
     * @return stdClass
     */
    public static function toStdClass($data): stdClass
    {
        //====================================================================//
        // Empty Value
        if (empty($data) || self::isEmpty($data)) {
            return new stdClass();
        }
        //====================================================================//
        // Create & Return Field Data as Json String
        $decoded = json_decode($data);
        if (!($decoded instanceof stdClass)) {
            return new stdClass();
        }

        return $decoded;
    }

    /**
     * Check if Inline Field is Empty
     *
     * @param null|string $data Field Data.
     *
     * @return bool
     */
    public static function isEmpty($data): bool
    {
        //====================================================================//
        // Empty Value
        if (empty($data) || ("[]" == $data) || ("{}" == $data)) {
            return true;
        }

        return false;
    }
}
