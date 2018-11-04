<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Splash\Models\Fields;

/**
 * @abstract    Fields Definition & Data Manager
 *              Collection of Basic STATIC Functions to Manage Splash Fields
 */
trait FieldsManagerTrait
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
     *   @return     array|null
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
     *  @return     array|null
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
    
    //==============================================================================
    //      LISTS FIELDS MANAGEMENT
    //==============================================================================

    /**
     *   @abstract   Check if this id is a list identifier
     *
     *   @param      string  $In        Data Type Name String
     *
     *   @return     array|false        Exploded List field Array or False
     */
    public static function isListField($In)
    {
        //====================================================================//
        // Safety Check
        if (empty($In)) {
            return false;
        }
        //====================================================================//
        // Detects Lists
        $list = explode(LISTSPLIT, $In);
        if (is_array($list) && (count($list)==2)) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            return array("fieldname" => $list[0],"listname" => $list[1]);
        }
        return false;
    }

    /**
     * @abstract   Retrieve Field Identifier from an List Field String
     *
     * @param      string      $ListFieldName      List Field Identifier String
     *
     * @return     string|false
     */
    public static function fieldName($ListFieldName)
    {
        //====================================================================//
        // Decode
        $Result     = self::isListField($ListFieldName);
        if (empty($Result)) {
            return false;
        }
        //====================================================================//
        // Return Field Identifier
        return   $Result["fieldname"];
    }

    /**
     * @abstract   Retrieve List Name from an List Field String
     *
     * @param      string      $ListFieldName      List Field Identifier String
     *
     * @return     string|false
     */
    public static function listName($ListFieldName)
    {
        //====================================================================//
        // Decode
        $Result     = self::isListField($ListFieldName);
        if (empty($Result)) {
            return false;
        }
        //====================================================================//
        // Return List Name
        return   $Result["listname"];
    }
    
    /**
     * @abstract   Retrieve Base Field Type from Field Type|Id String
     * @param      string       $Type               List Field Identifier String
     * @return     string|false
     */
    public static function baseType($Type)
    {
        //====================================================================//
        // Detect List Id Fields
        if (self::isListField($Type)) {
            $Type = self::fieldName($Type);
        }
        //====================================================================//
        // Detect Objects Id Fields
        if (self::isIdField($Type)) {
            $Type = self::objectId($Type);
        }
        return $Type;
    }
    
    //==============================================================================
    //      OBJECT ID FIELDS MANAGEMENT
    //==============================================================================
    
    /**
     * @abstract   Identify if field is Object Identifier Data & Decode Field
     * @param   string      $In     ObjectId Field String
     * @return  array|false         Exploded Object ID Field Array or False
     */
    public static function isIdField($In)
    {
        //====================================================================//
        // Safety Check
        if (empty($In)) {
            return false;
        }
        //====================================================================//
        // Detects ObjectId
        $list = explode(IDSPLIT, $In);
        if (is_array($list) && (count($list)==2)) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            $Out["ObjectId"]        = $list[0];
            $Out["ObjectType"]      = $list[1];
            return $Out;
        }
        return false;
    }
    
    /**
     * @abstract   Retrieve Object Id Name from an Object Identifier String
     * @param      string      $In      Object Identifier String
     * @return     string|false
     */
    public static function objectId($In)
    {
        //====================================================================//
        // decode
        $Result     = self::isIdField($In);
        if (empty($Result)) {
            return false;
        }
        //====================================================================//
        // Return List Name
        return   $Result["ObjectId"];
    }

    /**
     * @abstract   Retrieve Object Type Name from an Object Identifier String
     * @param      string      $In      Object Identifier String
     * @return     string|false
     */
    public static function objectType($In)
    {
        //====================================================================//
        // decode
        $Result     = self::isIdField($In);
        if (empty($Result)) {
            return false;
        }
        //====================================================================//
        // Return Field Identifier
        return   $Result["ObjectType"];
    }
    
    //==============================================================================
    //      OBJECTS DATA BLOCKS FUNCTIONS
    //==============================================================================
        
    /**
     *   @abstract   Extract Raw Field Data from an Object Data Block
     *
     *   @param      array      $DataBlock          Object Data Block
     *   @param      string      $Filter            Single Fields Id
     *
     *   @return     array|null
     */
    public static function extractRawData($DataBlock, $Filter)
    {
        $FilteredData   =   self::filterData($DataBlock, array($Filter));
        
        //====================================================================//
        // Explode List Field Id
        $List       =   self::isListField($Filter);
        
        //====================================================================//
        // Simple Single Field
        if (!$List) {
            if (isset($FilteredData[$Filter])) {
                return $FilteredData[$Filter];
            }
            
        //====================================================================//
        // List Field
        } else {
            //====================================================================//
            // Check List Exists
            if (!array_key_exists($List["listname"], $FilteredData)) {
                return null;
            }
            
            //====================================================================//
            // Parse Raw List Data
            $Result = array();
            foreach ($FilteredData[$List["listname"]] as $Key => $ListItem) {
                $Result[$Key]   =   $ListItem[$List["fieldname"]];
            }
            return $Result;
        }
        
        //====================================================================//
        // Field Not Received or is Empty
        return null;
    }
    
    /**
     *   @abstract   Filter a Object Data Block to keap only given Fields
     *
     *   @param      array      $DataBlock      Object Data Block
     *   @param      array      $Filters        Array of Fields Ids
     *
     *   @return     array|null
     */
    public static function filterData($DataBlock, $Filters = array())
    {
        $Result         =   array();
        $ListFilters    =   array();
        
        //====================================================================//
        // Process All Single Fields Ids & Store Sorted List Fields Ids
        foreach ($Filters as $FieldId) {
            //====================================================================//
            // Explode List Field Id
            $List       =   self::isListField($FieldId);
            //====================================================================//
            // Single Field Data Type
            if ((!$List) && (array_key_exists($FieldId, $DataBlock))) {
                $Result[$FieldId] = $DataBlock[$FieldId];
            } elseif (!$List) {
                continue;
            }
            //====================================================================//
            // List Field Data Type
            $ListName   =   $List["listname"];
            $FieldName  =   $List["fieldname"];
            //====================================================================//
            // Check List Data are Present in Block
            if (!array_key_exists($ListName, $DataBlock)) {
                continue;
            }
            //====================================================================//
            // Create List
            if (!array_key_exists($ListName, $ListFilters)) {
                $ListFilters[$ListName] = array();
            }
            $ListFilters[$ListName][] = $FieldName;
        }
        
        //====================================================================//
        // Process All List Fields Ids Filters
        foreach ($ListFilters as $ListName => $ListFilters) {
            $Result[$ListName] = self::filterListData($DataBlock[$ListName], $ListFilters);
        }
        
        return $Result;
    }
    
    /**
     *   @abstract   Filter a Object List Data Block to keap only given Fields
     *
     *   @param      array      $ListBlock  Object Data Block
     *   @param      array      $Filters    Array of Fields Ids
     *
     *   @return     array
     */
    public static function filterListData($ListBlock, $Filters = array())
    {
        $Result =   array();
        
        foreach ($ListBlock as $ItemBlock) {
            $FilteredItems = array();
            
            //====================================================================//
            // Search for Field in Item Block
            if (!is_array($ItemBlock) && !is_a($ItemBlock, "ArrayObject")) {
                continue;
            }
            
            //====================================================================//
            // Search for Field in Item Block
            foreach ($Filters as $FieldId) {
                if (array_key_exists($FieldId, $ItemBlock)) {
                    $FilteredItems[$FieldId] = $ItemBlock[$FieldId];
                }
            }
            
            $Result[] = $FilteredItems;
        }
        
        return $Result;
    }
    
    /**
     *  @abstract   Normalize An Object Data Block (ie: before Compare)
     *
     *  @param      mixed       $In      Input Array
     *
     *  @return     array                   Sorted Array
     */
    public static function normalize(&$In)
    {
       
        //==============================================================================
        //      Convert ArrayObjects To Simple Array
        if (is_a($In, "ArrayObject")) {
            $In = $In->getArrayCopy();
            //==============================================================================
            // Normalize Contents
            self::normalize($In);
            
        //==============================================================================
        // Normalize Array Contents
        } elseif (is_array($In)) {
            foreach ($In as &$value) {
                self::normalize($value);
            }
            
        //==============================================================================
        // Normalize Bool as Strings
        } elseif (is_bool($In)) {
            $In = $In?"1":"0";
            
        //==============================================================================
        // Normalize Numbers as Strings
        } elseif (is_numeric($In)) {
            $In = strval($In);
        }
        
        return $In;
    }
    
    /**
    *   @abstract   kSort of An Object Data Block (ie: before Compare)
    *
    *   @param      array       $In      Input Array
    *
    *   @return     array                   Sorted Array
    */
    public static function sort(&$In)
    {
        if (!is_array($In)) {
            return $In;
        }
            
        //==============================================================================
        // Sort All Sub-Contents
        foreach ($In as &$value) {
            if (is_array($value)) {
                self::sort($value);
            }
        }
        return ksort($In);
    }
}
