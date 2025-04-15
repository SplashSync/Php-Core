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

/**
 * Helper for Building Checksum for Objects, Array, and more...
 */
class ChecksumHelper
{
    /**
     * Compute Md5 CheckSum from Object Attributes
     *
     * @param null|array $input Array of Object Data ($Code => $Value)
     *
     * @return null|string Unique Md5 Checksum
     */
    public static function fromArray(array $input = null): ?string
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
     * Compute Md5 CheckSum from Arguments
     *
     * @return null|string Unique Md5 Checksum
     */
    public static function fromValues(): ?string
    {
        //====================================================================//
        // Return Encoded CheckSum from Function Args
        return self::fromArray(func_get_args());
    }

    /**
     * Compute Debug CheckSum String from Object Attributes
     *
     * @param null|array $input Array of Object Data ($Code => $Value)
     *
     * @return null|string Unique String Checksum
     */
    public static function debugFromArray(array $input = null): ?string
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
     * Compute Debug CheckSum String from Arguments
     *
     * @return null|string Unique String Checksum
     */
    public static function debugFromValues(): ?string
    {
        //====================================================================//
        // Return Debug CheckSum String from Function Args
        return self::debugFromArray(func_get_args());
    }

    /**
     * Verify inputs
     *
     * @param array $input Array of Object Data ($code => $value)
     */
    private static function isValid(array $input): bool
    {
        foreach ($input as $value) {
            if (!is_scalar($value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Encode CheckSum from Array
     *
     * @param array $input Array of Object Data ($Code => $Value)
     *
     * @return string Unique Md5 Checksum
     */
    private static function getEncoded(array $input): string
    {
        //====================================================================//
        // Sort this Array by Keys
        ksort($input);

        //====================================================================//
        // Serialize Array & Encode Checksum
        return md5(strtolower(serialize($input)));
    }

    /**
     * Encode CheckSum from Array
     *
     * @param array $input Array of Object Data ($code => $value)
     *
     * @return string Unique String Checksum
     */
    private static function getDebug(array $input): string
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
