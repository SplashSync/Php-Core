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
 * @abstract    Helper for Objects Fields Management
 */
class ObjectsHelper
{
   
    /**
     * @abstract   Create an Object Identifier String
     *
     * @param   string      $objectType     Object Type Name.
     * @param   string      $objectId       Object Identifier
     *
     * @return  string
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
        return   $objectId . IDSPLIT . $objectType;
    }
    
    /**
     *      @abstract   Decode an Object Identifier String
     *
     *      @param      string      $objectId           Object Identifier String.
     *
     *      @return     string
     */
    private static function decode($objectId)
    {
        // Safety Checks
        if (empty($objectId)) {
            return false;
        }
        // Explode Object String
        $result = explode(IDSPLIT, $objectId);
        // Check result is Valid
        if (count($result) != 2) {
            return false;
        }
        // Return Object Array
        return   $result;
    }
    
    /**
     * @abstract   Retrieve Identifier from an Object Identifier String
     * @param      string      $objectId           Object Identifier String.
     * @return     string
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function id($objectId)
    {
        //====================================================================//
        // Decode
        $result     = self::decode($objectId);
        if (empty($result)) {
            return false;
        }
        //====================================================================//
        // Return Object Identifier
        return   $result[0];
    }

    /**
     *      @abstract   Retrieve Object Type Name from an Object Identifier String
     *
     *      @param      string      $objectId           Object Identifier String.
     *
     *      @return     string
     */
    public static function type($objectId)
    {
        //====================================================================//
        // Decode
        $result     = self::decode($objectId);
        if (empty($result)) {
            return false;
        }
        //====================================================================//
        // Return Object Type Name
        return   $result[1];
    }
}
