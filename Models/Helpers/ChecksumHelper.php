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

/**
 * @abstract    Helper for Building Checksum for Objects, Array, and more...
 */
class ChecksumHelper
{
    /**
     * @abstract    Compute Md5 CheckSum from Object Attributes
     *
     * @param array $input Array of Object Data ($Code => $Value)
     *
     * @return null|string Unik Md5 Checksum
     */
    public static function fromArray($input = null)
    {
        //====================================================================//
        // Safety Check
        if (is_null($input) || !self::isValid($input)) {
            return null;
        }
        //====================================================================//
        // return Encoded CheckSum
        return self::getEncoded($input);
    }

    /**
     * @abstract    Compute Md5 CheckSum from Arguments
     *
     * @return null|string Unik Md5 Checksum
     */
    public static function fromValues()
    {
        //====================================================================//
        // Return Encoded CheckSum from Function Args
        return self::fromArray(func_get_args());
    }

    /**
     * @abstract    Compute Debug CheckSum String from Object Attributes
     *
     * @param array $input Array of Object Data ($Code => $Value)
     *
     * @return null|string Unik String Checksum
     */
    public static function debugFromArray($input = null)
    {
        //====================================================================//
        // Safety Check
        if (is_null($input) || !self::isValid($input)) {
            return null;
        }
        //====================================================================//
        // return Encoded CheckSum
        return self::getDebug($input);
    }

    /**
     * @abstract    Compute Debug CheckSum String from Arguments
     *
     * @return null|string Unik String Checksum
     */
    public static function debugFromValues()
    {
        //====================================================================//
        // Return Debug CheckSum String from Function Args
        return self::debugFromArray(func_get_args());
    }

    /**
     * @abstract    Verify inputs
     *
     * @param array $input Array of Object Data ($Code => $Value)
     *
     * @return bool
     */
    private static function isValid($input)
    {
        if (!is_array($input)) {
            return false;
        }
        foreach ($input as $value) {
            if (!is_scalar($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @abstract    Encode CheckSum from Array
     *
     * @param array $input Array of Object Data ($Code => $Value)
     *
     * @return string Unik Md5 Checksum
     */
    private static function getEncoded($input)
    {
        //====================================================================//
        // Sort this Array by Keys
        ksort($input);
        //====================================================================//
        // Serialize Array & Encode Checksum
        return md5(strtolower(serialize($input)));
    }

    /**
     * @abstract    Encode CheckSum from Array
     *
     * @param array $input Array of Object Data ($Code => $Value)
     *
     * @return string Unik String Checksum
     */
    private static function getDebug($input)
    {
        //====================================================================//
        // Sort this Array by Keys
        ksort($input);
        //====================================================================//
        // Build CheckSum Debug Array
        $debugArray = array();
        foreach ($input as $key => $value) {
            $debugArray[] = strtolower($key);
            $debugArray[] = strtolower($value);
        }

        //====================================================================//
        // Implode Debug Array
        return implode("|", $debugArray);
    }
}
