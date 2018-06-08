<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2017 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 *
 **/

namespace   Splash\Models\Helpers;

/**
 * @abstract    Helper for Building Checksum for Objects, Array, and more...
 */
class ChecksumHelper
{
    
    /**
     * @abstract    Compute Md5 CheckSum from Object Attributes
     * @param       array       $Input      Array of Object Data ($Code => $Value)
     * @return      string                  Unik Md5 Checksum
     */
    public static function fromArray($Input = null)
    {
        //====================================================================//
        // Safety Check
        if (!self::isValid($Input)) {
            return null;
        }
        //====================================================================//
        // return Encoded CheckSum
        return self::getEncoded($Input);
    }
    
    /**
     * @abstract    Compute Md5 CheckSum from Arguments
     * @return      string                  Unik Md5 Checksum
     */
    public static function fromValues()
    {
        //====================================================================//
        // Return Encoded CheckSum from Function Args
        return self::fromArray(func_num_args());
    }
    
    /**
     * @abstract    Compute Debug CheckSum String from Object Attributes
     * @param       array       $Input      Array of Object Data ($Code => $Value)
     * @return      string                  Unik String Checksum
     */
    public static function debugFromArray($Input = null)
    {
        //====================================================================//
        // Safety Check
        if (!self::isValid($Input)) {
            return null;
        }
        //====================================================================//
        // return Encoded CheckSum
        return self::getDebug($Input);
    }

    /**
     * @abstract    Compute Debug CheckSum String from Arguments
     * @return      string                  Unik String Checksum
     */
    public static function debugFromValues()
    {
        //====================================================================//
        // Return Debug CheckSum String from Function Args
        return self::debugFromArray(func_num_args());
    }
    
    /**
     * @abstract    Verify inputs
     * @param       array       $Input      Array of Object Data ($Code => $Value)
     * @return      bool
     */
    private static function isValid($Input)
    {
        if (!is_array($Input) && !is_a($Input, "ArrayObject")) {
            return false;
        }
        foreach ($Input as $Value) {
            if (!is_scalar($Value)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @abstract    Encode CheckSum from Array
     * @param       array       $Input      Array of Object Data ($Code => $Value)
     * @return      string                  Unik Md5 Checksum
     */
    private static function getEncoded($Input)
    {
        //====================================================================//
        // Sort this Array by Keys
        ksort($Input);
        //====================================================================//
        // Serialize Array & Encode Checksum
        return md5(serialize($Input));
    }
    
    /**
     * @abstract    Encode CheckSum from Array
     * @param       array       $Input      Array of Object Data ($Code => $Value)
     * @return      string                  Unik String Checksum
     */
    private static function getDebug($Input)
    {
        //====================================================================//
        // Sort this Array by Keys
        ksort($Input);
        //====================================================================//
        // Build CheckSum Debug Array
        $DebugArray =   array();
        foreach ($Input as $Key => $Value) {
            $DebugArray[]   =   $Key;
            $DebugArray[]   =   $Value;
        }
        
        //====================================================================//
        // Implode Debug Array
        return implode("|", $DebugArray);
    }
}
