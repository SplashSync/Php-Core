<?php

namespace Splash\Tests\Tools\Traits;

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
     *  @abstract   Filter a Fields List to keap only given Fields Tags
     *
     *  @param      array       $FieldsList     Object Field List
     *  @param      string      $ItemType       Field Microdata Type Url
     *  @param      string      $ItemProp       Field Microdata Property Name
     *
     *  @return     array
     */
    public static function filterFieldListByTag($FieldsList, $ItemType, $ItemProp)
    {
        $Result     =   array();
        $FilterTag  =   md5($ItemProp . IDSPLIT . $ItemType);
        
        foreach ($FieldsList as $Field) {
            if ($Field->tag !== $FilterTag) {
                continue;
            }
            if (($Field->itemtype !== $ItemType) || ($Field->itemprop !== $ItemProp)) {
                continue;
            }
            $Result[] = $Field;
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
     *  @abstract   Find a Field Definition in List by Id
     *
     *  @param      array      $FieldsList     Object Field List
     *  @param      string     $ItemType       Field Microdata Type Url
     *  @param      string     $ItemProp       Field Microdata Property Name
     *
     *  @return     array
     */
    public static function findFieldByTag($FieldsList, $ItemType, $ItemProp)
    {
        $Fields = self::filterFieldListByTag($FieldsList, $ItemType, $ItemProp);
        
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
