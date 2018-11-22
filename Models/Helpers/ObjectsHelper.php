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

use Splash\Models\Fields\FieldsManagerTrait;

/**
 * @abstract    Helper for Objects Fields Management
 */
class ObjectsHelper
{
    use FieldsManagerTrait;
   
    /**
     * @abstract   Create an Object Identifier String
     *
     * @param   string      $objectType     Object Type Name.
     * @param   string      $objectId       Object Identifier
     *
     * @return     false|string
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
     * @abstract   Retrieve Identifier from an Object Identifier String
     * @param      string      $objectId           Object Identifier String.
     * @return     false|string
     * @SuppressWarnings(PHPMD.ShortMethodName)
     */
    public static function id($objectId)
    {
        //====================================================================//
        // Forward to Fields Manager
        return   self::objectId($objectId);
    }

    /**
     *      @abstract   Retrieve Object Type Name from an Object Identifier String
     *
     *      @param      string      $objectId           Object Identifier String.
     *
     *      @return     string|false
     */
    public static function type($objectId)
    {
        //====================================================================//
        // Forward to Fields Manager
        return   self::objectType($objectId);
    }
}
