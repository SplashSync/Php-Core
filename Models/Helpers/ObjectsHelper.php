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
     *      @abstract   Create an Object Identifier String
     *
     *      @param      string      $ObjectType     Object Type Name.
     *      @param      string      $Identifier     Object Identifier
     *
     *      @return     string
     */
    public static function encode($ObjectType, $Identifier)
    {
        //====================================================================//
        // Safety Checks
        if (empty($ObjectType)) {
            return false;
        }
        if (empty($Identifier)) {
            return false;
        }
        
        //====================================================================//
        // Create & Return Field Id Data String
        return   $Identifier . IDSPLIT . $ObjectType;
    }
    
    /**
     *      @abstract   Decode an Object Identifier String
     *
     *      @param      string      $ObjectId           Object Identifier String.
     *
     *      @return     string
     */
    private static function decode($ObjectId)
    {
        // Safety Checks
        if (empty($ObjectId)) {
            return false;
        }
        // Explode Object String
        $Result = explode(IDSPLIT, $ObjectId);
        // Check result is Valid
        if (count($Result) != 2) {
            return false;
        }
        // Return Object Array
        return   $Result;
    }
    
    /**
     * @abstract   Retrieve Identifier from an Object Identifier String
     * @param      string      $ObjectId           Object Identifier String.
     * @return     string
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function id($ObjectId)
    {
        //====================================================================//
        // Decode
        $Result     = self::decode($ObjectId);
        if (empty($Result)) {
            return false;
        }
        //====================================================================//
        // Return Object Identifier
        return   $Result[0];
    }

    /**
     *      @abstract   Retrieve Object Type Name from an Object Identifier String
     *
     *      @param      string      $ObjectId           Object Identifier String.
     *
     *      @return     string
     */
    public static function type($ObjectId)
    {
        //====================================================================//
        // Decode
        $Result     = self::decode($ObjectId);
        if (empty($Result)) {
            return false;
        }
        //====================================================================//
        // Return Object Type Name
        return   $Result[1];
    }
}
