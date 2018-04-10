<?php

namespace Splash\Tests\Tools\Traits;

use Splash\Client\Splash;
use Splash\Tests\Tools\Fields\Ooobjectid as ObjectId;

/**
 * @abstract    Splash Test Tools - Objects Fields Management
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsFieldsTrait
{
    //==============================================================================
    //      FIELDS LIST FUNCTIONS
    //==============================================================================
    
    /**
     *   @abstract   Filter a Fields List to keap only given Fields Ids
     *
     *   @param      array      $FieldsList     Object Field List
     *   @param      array      $Filters        Array of Fields Ids
     *
     *   @return     array
     */
    public static function filterFieldList($FieldsList, $Filters = array())
    {
        $Result =   array();
        
        foreach ($FieldsList as $Field) {
            if (in_array($Field->id, $Filters)) {
                $Result[] = $Field;
            }
        }
        
        return $Result;
    }
    
    /**
     *   @abstract   Find a Field Definition in List by Id
     *
     *   @param      array      $FieldsList     Object Field List
     *   @param      array      $FieldId        Field Id
     *
     *   @return     array
     */
    public static function findField($FieldsList, $FieldId)
    {
        $Fields = self::filterFieldList($FieldsList, $FieldId);
        
        if (count($Fields) != 1) {
            return null;
        }
                
        return array_shift($Fields);
    }

    /**
     *   @abstract   Redure a Fields List to an Array of Field Ids
     *
     *   @param      array      $FieldsList     Object Field List
     *   @param      bool       $isRead         Filter non Readable Fields
     *   @param      bool       $isWrite        Filter non Writable Fields
     *
     *   @return     array
     */
    public static function reduceFieldList($FieldsList, $isRead = false, $isWrite = false)
    {
        $Result =   array();
       
        foreach ($FieldsList as $Field) {
            //==============================================================================
            //      Filter Non-Readable Fields
            if ($isRead && !$Field->read) {
                continue;
            }
            //==============================================================================
            //      Filter Non-Writable Fields
            if ($isWrite && !$Field->write) {
                continue;
            }
            $Result[] = $Field->id;
        }
            
        return $Result;
    }
}
