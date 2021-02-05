<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Models\Fields;

use ArrayObject;

/**
 * Fields Definition & Data Manager
 * Collection of Basic STATIC Functions to Manage Splash Fields
 */
trait FieldsManagerTrait
{
    //==============================================================================
    //      FIELDS LIST FUNCTIONS
    //==============================================================================

    /**
     * Filter a Fields List to keap only given Fields Ids
     *
     * @param array $fieldsList Object Field List
     * @param array $filters    Array of Fields Ids
     *
     * @return array
     */
    public static function filterFieldList($fieldsList, $filters = array())
    {
        $result = array();

        foreach ($fieldsList as $field) {
            if (in_array($field->id, $filters, true)) {
                $result[] = $field;
            }
        }

        return $result;
    }

    /**
     * Filter a Fields List to keap only given Fields Tags
     *
     * @param array  $fieldsList Object Field List
     * @param string $itemType   Field Microdata Type Url
     * @param string $itemProp   Field Microdata Property Name
     *
     * @return array
     */
    public static function filterFieldListByTag($fieldsList, $itemType, $itemProp)
    {
        $result = array();
        $tag = md5($itemProp.IDSPLIT.$itemType);

        foreach ($fieldsList as $field) {
            if ($field->tag !== $tag) {
                continue;
            }
            if (($field->itemtype !== $itemType) || ($field->itemprop !== $itemProp)) {
                continue;
            }
            $result[] = $field;
        }

        return $result;
    }

    /**
     * Find a Field Definition in List by Id
     *
     * @param array $fieldsList Object Field List
     * @param array $fieldId    Field Id
     *
     * @return null|ArrayObject
     */
    public static function findField($fieldsList, $fieldId)
    {
        $fields = self::filterFieldList($fieldsList, $fieldId);

        if (1 != count($fields)) {
            return null;
        }

        return array_shift($fields);
    }

    /**
     * Find a Field Definition in List by Id
     *
     * @param array  $fieldsList Object Field List
     * @param string $itemType   Field Microdata Type Url
     * @param string $itemProp   Field Microdata Property Name
     *
     * @return null|ArrayObject
     */
    public static function findFieldByTag($fieldsList, $itemType, $itemProp)
    {
        $fields = self::filterFieldListByTag($fieldsList, $itemType, $itemProp);

        if (1 != count($fields)) {
            return null;
        }

        return array_shift($fields);
    }

    /**
     * Redure a Fields List to an Array of Field Ids
     *
     * @param array $fieldsList Object Field List
     * @param bool  $isRead     Filter non Readable Fields
     * @param bool  $isWrite    Filter non Writable Fields
     *
     * @return string[]
     */
    public static function reduceFieldList($fieldsList, $isRead = false, $isWrite = false)
    {
        $result = array();

        foreach ($fieldsList as $field) {
            //==============================================================================
            //      Filter Non-Readable Fields
            if ($isRead && !$field->read) {
                continue;
            }
            //==============================================================================
            //      Filter Non-Writable Fields
            if ($isWrite && !$field->write) {
                continue;
            }
            $result[] = $field->id;
        }

        return $result;
    }

    //==============================================================================
    //      LISTS FIELDS MANAGEMENT
    //==============================================================================

    /**
     * Check if this id is a list identifier
     *
     * @param string $fieldType Data Type Name String
     *
     * @return array|false Exploded List field Array or False
     */
    public static function isListField($fieldType)
    {
        //====================================================================//
        // Safety Check
        if (empty($fieldType)) {
            return false;
        }
        //====================================================================//
        // Detects Lists
        $list = explode(LISTSPLIT, $fieldType);
        if (is_array($list) && (2 == count($list))) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            return array('fieldname' => $list[0], 'listname' => $list[1]);
        }

        return false;
    }

    /**
     * Retrieve Field Identifier from an List Field String
     *
     * @param string $listFieldName List Field Identifier String
     *
     * @return false|string
     */
    public static function fieldName($listFieldName)
    {
        //====================================================================//
        // Decode
        $result = self::isListField($listFieldName);
        if (empty($result)) {
            return false;
        }
        //====================================================================//
        // Return Field Identifier
        return   $result['fieldname'];
    }

    /**
     * Retrieve List Name from an List Field String
     *
     * @param string $listFieldName List Field Identifier String
     *
     * @return false|string
     */
    public static function listName($listFieldName)
    {
        //====================================================================//
        // Decode
        $result = self::isListField($listFieldName);
        if (empty($result)) {
            return false;
        }
        //====================================================================//
        // Return List Name
        return   $result['listname'];
    }

    /**
     * Retrieve Base Field Type from Field Type|Id String
     *
     * @param string $fieldId List Field Identifier String
     *
     * @return false|string
     */
    public static function baseType($fieldId)
    {
        //====================================================================//
        // Detect List Id Fields
        if (self::isListField($fieldId)) {
            $fieldId = self::fieldName($fieldId);
        }
        //====================================================================//
        // Detect Objects Id Fields
        if (self::isIdField((string) $fieldId)) {
            $fieldId = self::objectType((string) $fieldId);
        }

        return $fieldId;
    }

    //==============================================================================
    //      OBJECT ID FIELDS MANAGEMENT
    //==============================================================================

    /**
     * Identify if field is Object Identifier Data & Decode Field
     *
     * @param string $fieldId ObjectId Field String
     *
     * @return array|false
     */
    public static function isIdField($fieldId)
    {
        //====================================================================//
        // Safety Check
        if (empty($fieldId)) {
            return false;
        }
        //====================================================================//
        // Detects ObjectId
        $list = explode(IDSPLIT, $fieldId);
        if (is_array($list) && (2 == count($list))) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            $result['ObjectId'] = $list[0];
            $result['ObjectType'] = $list[1];

            return $result;
        }

        return false;
    }

    /**
     * Retrieve Object Id Name from an Object Identifier String
     *
     * @param string $fieldId Object Identifier String
     *
     * @return false|string
     */
    public static function objectId($fieldId)
    {
        //====================================================================//
        // decode
        $result = self::isIdField($fieldId);
        if (empty($result)) {
            return false;
        }
        //====================================================================//
        // Return List Name
        return   $result['ObjectId'];
    }

    /**
     * Retrieve Object Type Name from an Object Identifier String
     *
     * @param string $fieldId Object Identifier String
     *
     * @return false|string
     */
    public static function objectType($fieldId)
    {
        //====================================================================//
        // decode
        $result = self::isIdField($fieldId);
        if (empty($result)) {
            return false;
        }
        //====================================================================//
        // Return Field Identifier
        return   $result['ObjectType'];
    }

    //==============================================================================
    //      OBJECTS DATA BLOCKS FUNCTIONS
    //==============================================================================

    /**
     * Extract Raw Field Data from an Object Data Block
     *
     * @param array  $objectData Object Data Block
     * @param string $filter     Single Fields Id
     *
     * @return null|array
     */
    public static function extractRawData($objectData, $filter)
    {
        $filteredData = self::filterData($objectData, array($filter));

        //====================================================================//
        // Explode List Field Id
        $isList = self::isListField($filter);

        //====================================================================//
        // Simple Single Field
        if (!$isList) {
            if (isset($filteredData[$filter])) {
                return $filteredData[$filter];
            }

            //====================================================================//
        // List Field
        } else {
            //====================================================================//
            // Check List Exists
            if (!isset($filteredData[self::listName($filter)])) {
                return null;
            }

            //====================================================================//
            // Parse Raw List Data
            $result = array();
            foreach ($filteredData[self::listName($filter)] as $key => $item) {
                $result[$key] = $item[self::fieldName($filter)];
            }

            return $result;
        }

        //====================================================================//
        // Field Not Received or is Empty
        return null;
    }

    /**
     * Filter a Object Data Block to keap only given Fields
     *
     * @param array $objectData Object Data Block
     * @param array $filters    Array of Fields Ids
     *
     * @return null|array
     */
    public static function filterData($objectData, $filters = array())
    {
        $result = array();
        $listFilters = array();

        //====================================================================//
        // Process All Single Fields Ids & Store Sorted List Fields Ids
        foreach ($filters as $fieldId) {
            //====================================================================//
            // Explode List Field Id
            $isList = self::isListField($fieldId);
            //====================================================================//
            // Single Field Data Type
            if (!is_array($isList)) {
                if (!array_key_exists($fieldId, $objectData)) {
                    continue;
                }
                $result[$fieldId] = $objectData[$fieldId];
            }
            //====================================================================//
            // List Field Data Type
            $listName = is_array($isList) ? $isList['listname'] : null;
            $fieldName = is_array($isList) ? $isList['fieldname'] : null;
            //====================================================================//
            // Check List Data are Present in Block
            if (!array_key_exists($listName, $objectData)) {
                continue;
            }
            //====================================================================//
            // Create List
            if (!array_key_exists($listName, $listFilters)) {
                $listFilters[$listName] = array();
            }
            $listFilters[$listName][] = $fieldName;
        }

        //====================================================================//
        // Process All List Fields Ids Filters
        foreach ($listFilters as $listName => $listFilters) {
            $result[$listName] = self::filterListData($objectData[$listName], $listFilters);
        }

        return $result;
    }

    /**
     * Filter a Object List Data Block to keap only given Fields
     *
     * @param null|array|ArrayObject|string $objectData Object Data Block
     * @param array                         $filters    Array of Fields Ids
     *
     * @return array
     */
    public static function filterListData($objectData, $filters = array())
    {
        $result = array();
        //====================================================================//
        // Safety Check => List Data is not Empty
        if (!is_array($objectData) && !($objectData instanceof ArrayObject)) {
            return $result;
        }
        //====================================================================//
        // Walk on List Data Items
        foreach ($objectData as $fieldData) {
            $filteredItems = array();
            //====================================================================//
            // Ensure Item is An Array of Fields
            if (!is_array($fieldData) && !($fieldData instanceof ArrayObject)) {
                continue;
            }
            //====================================================================//
            // Convert ArrayObjects to Array
            if ($fieldData instanceof ArrayObject) {
                $fieldData = $fieldData->getArrayCopy();
            }
            //====================================================================//
            // Search for Field in Item Block
            foreach ($filters as $fieldId) {
                if (array_key_exists($fieldId, $fieldData)) {
                    $filteredItems[$fieldId] = $fieldData[$fieldId];
                }
            }
            $result[] = $filteredItems;
        }

        return $result;
    }

    /**
     * Normalize An Object Data Block (ie: before Compare)
     *
     * @param mixed $input Input Array
     *
     * @return array Sorted Array
     */
    public static function normalize(&$input)
    {
        //==============================================================================
        //      Convert ArrayObjects To Simple Array
        if ($input instanceof ArrayObject) {
            $input = $input->getArrayCopy();
            //==============================================================================
            // Normalize Contents
            self::normalize($input);

        //==============================================================================
        // Normalize Array Contents
        } elseif (is_array($input)) {
            foreach ($input as &$value) {
                self::normalize($value);
            }

            //==============================================================================
        // Normalize Bool as Strings
        } elseif (is_bool($input)) {
            $input = $input ? '1' : '0';

        //==============================================================================
        // Normalize Numbers as Strings
        } elseif (is_numeric($input)) {
            $input = strval($input);
        }

        return $input;
    }

    /**
     * kSort of An Object Data Block (ie: before Compare)
     *
     * @param array $inputArray Input Array
     *
     * @return array Sorted Array
     */
    public static function sort(&$inputArray)
    {
        if (!is_array($inputArray)) {
            return $inputArray;
        }
        //==============================================================================
        // Sort All Sub-Contents
        foreach ($inputArray as &$value) {
            if (is_array($value)) {
                self::sort($value);
            }
        }
        //==============================================================================
        // Sort Main Contents
        ksort($inputArray);

        return $inputArray;
    }
}
