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
     * Filter a Fields List to keep only given Fields Ids
     *
     * @param array[]  $fieldsList Object Field List
     * @param string[] $filters    Array of Fields Ids
     *
     * @return array[]
     */
    public static function filterFieldList(array $fieldsList, array $filters = array()): array
    {
        $result = array();

        foreach ($fieldsList as $field) {
            if (in_array($field["id"], $filters, true)) {
                $result[] = $field;
            }
        }

        return $result;
    }

    /**
     * Filter a Fields List to keep only given Fields Tags
     *
     * @param array[] $fieldsList Object Field List
     * @param string  $itemType   Field Microdata Type Url
     * @param string  $itemProp   Field Microdata Property Name
     *
     * @return array[]
     */
    public static function filterFieldListByTag(array $fieldsList, string $itemType, string $itemProp): array
    {
        $result = array();
        $tag = md5($itemProp.IDSPLIT.$itemType);

        foreach ($fieldsList as $field) {
            if ($field["tag"] !== $tag) {
                continue;
            }
            if (($field["itemtype"] !== $itemType) || ($field["itemprop"] !== $itemProp)) {
                continue;
            }
            $result[] = $field;
        }

        return $result;
    }

    /**
     * Find a Field Definition in List by ID
     *
     * @param array[]  $fieldsList Object Field List
     * @param string[] $fieldId    Field Id
     *
     * @return null|array
     */
    public static function findField(array $fieldsList, array $fieldId): ?array
    {
        $fields = self::filterFieldList($fieldsList, $fieldId);

        if (1 != count($fields)) {
            return null;
        }

        return array_shift($fields);
    }

    /**
     * Find Primary Field Definition in a List
     *
     * @param array[] $fieldsList Object Field List
     *
     * @return null|array
     */
    public static function findPrimaryField(array $fieldsList): ?array
    {
        $primaryFields = self::findPrimaryFields($fieldsList);

        if (1 != count($primaryFields)) {
            return null;
        }

        return array_shift($primaryFields);
    }

    /**
     * Find All Primary Fields in a List
     *
     * @param array[] $fieldsList Object Field List
     *
     * @return array<string, array>
     */
    public static function findPrimaryFields(array $fieldsList): array
    {
        $primaryFields = array();
        //==============================================================================
        // Walk on Given Fields
        foreach ($fieldsList as $field) {
            if (!empty($field["primary"])) {
                $primaryFields[(string) $field["id"]] = $field;
            }
        }

        return $primaryFields;
    }

    /**
     * Find a Field Definition in List by ID
     *
     * @param array[] $fieldsList Object Field List
     * @param string  $itemType   Field Microdata Type Url
     * @param string  $itemProp   Field Microdata Property Name
     *
     * @return null|array
     */
    public static function findFieldByTag(array $fieldsList, string $itemType, string $itemProp): ?array
    {
        $fields = self::filterFieldListByTag($fieldsList, $itemType, $itemProp);

        if (1 != count($fields)) {
            return null;
        }

        return array_shift($fields);
    }

    /**
     * Reduce a Fields List to an Array of Field Ids
     *
     * @param array[] $fieldsList Object Field List
     * @param bool    $isRead     Filter non Readable Fields
     * @param bool    $isWrite    Filter non Writable Fields
     *
     * @return string[]
     */
    public static function reduceFieldList(array $fieldsList, bool $isRead = false, bool $isWrite = false): array
    {
        $result = array();

        foreach ($fieldsList as $field) {
            //==============================================================================
            //      Filter Non-Readable Fields
            if ($isRead && !$field["read"]) {
                continue;
            }
            //==============================================================================
            //      Filter Non-Writable Fields
            if ($isWrite && !$field["write"]) {
                continue;
            }
            $result[] = $field["id"];
        }

        return $result;
    }

    //==============================================================================
    //      LISTS FIELDS MANAGEMENT
    //==============================================================================

    /**
     * Check if this id is a list identifier
     *
     * @param null|string $fieldType Data Type Name String
     *
     * @return null|array<string, string> Exploded List field Array or False
     */
    public static function isListField(?string $fieldType): ?array
    {
        //====================================================================//
        // Safety Check
        if (empty($fieldType)) {
            return null;
        }
        //====================================================================//
        // Detects Lists
        $list = explode(LISTSPLIT, $fieldType);
        if (is_array($list) && (2 == count($list))) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            return array('fieldname' => $list[0], 'listname' => $list[1]);
        }

        return null;
    }

    /**
     * Retrieve Field Identifier from a List Field String
     *
     * @param null|string $listFieldName List Field Identifier String
     *
     * @return null|string
     */
    public static function fieldName(?string $listFieldName): ?string
    {
        //====================================================================//
        // Decode
        $result = self::isListField($listFieldName);
        if (empty($result)) {
            return null;
        }
        //====================================================================//
        // Return Field Identifier
        return $result['fieldname'];
    }

    /**
     * Retrieve List Name from an List Field String
     *
     * @param null|string $listFieldName List Field Identifier String
     *
     * @return null|string
     */
    public static function listName(?string $listFieldName): ?string
    {
        //====================================================================//
        // Decode
        $result = self::isListField($listFieldName);
        if (empty($result)) {
            return null;
        }
        //====================================================================//
        // Return List Name
        return $result['listname'];
    }

    /**
     * Retrieve Base Field Type from Field Type|Id String
     *
     * @param null|string $fieldId List Field Identifier String
     *
     * @return null|string
     */
    public static function baseType(?string $fieldId): ?string
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

        return $fieldId ?: null;
    }

    //==============================================================================
    //      OBJECT ID FIELDS MANAGEMENT
    //==============================================================================

    /**
     * Identify if field is Object Identifier Data & Decode Field
     *
     * @param null|string $fieldId ObjectId Field String
     *
     * @return null|array
     */
    public static function isIdField(?string $fieldId): ?array
    {
        //====================================================================//
        // Safety Check
        if (empty($fieldId)) {
            return null;
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

        return null;
    }

    /**
     * Retrieve Object ID Name from an Object Identifier String
     *
     * @param null|string $fieldId Object Identifier String
     *
     * @return null|string
     */
    public static function objectId(?string $fieldId): ?string
    {
        //====================================================================//
        // decode
        $result = self::isIdField($fieldId);
        if (empty($result)) {
            return null;
        }
        //====================================================================//
        // Return List Name
        return $result['ObjectId'];
    }

    /**
     * Retrieve Object Type Name from an Object Identifier String
     *
     * @param null|string $fieldId Object Identifier String
     *
     * @return null|string
     */
    public static function objectType(?string $fieldId): ?string
    {
        //====================================================================//
        // decode
        $result = self::isIdField($fieldId);
        if (empty($result)) {
            return null;
        }
        //====================================================================//
        // Return Field Identifier
        return $result['ObjectType'];
    }

    //==============================================================================
    //      OBJECTS DATA BLOCKS FUNCTIONS
    //==============================================================================

    /**
     * Extract Raw Field Data from an Object Data Block
     *
     * @param array       $objectData Object Data Block
     * @param null|string $filter     Single Fields Id
     *
     * @return null|array|scalar
     */
    public static function extractRawData(array $objectData, ?string $filter)
    {
        //====================================================================//
        // Explode List Field Id
        if (empty($filter)) {
            return null;
        }

        $filteredData = self::filterData($objectData, array($filter));

        //====================================================================//
        // Explode List Field Id
        $isList = self::isListField($filter);

        //====================================================================//
        // Simple Single Field
        //====================================================================//
        if (!$isList) {
            if (isset($filteredData[$filter])) {
                return $filteredData[$filter];
            }
        } else {
            //====================================================================//
            // List Field
            //====================================================================//

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
     * Filter an Object Data Block to keep only given Fields
     *
     * @param array    $objectData Object Data Block
     * @param string[] $filters    Array of Fields Ids
     *
     * @return null|array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function filterData(array $objectData, array $filters = array()): ?array
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
            if (!$listName || !array_key_exists($listName, $objectData)) {
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
        foreach ($listFilters as $listName => $listFilter) {
            $result[$listName] = is_array($objectData[$listName])
                ? self::filterListData($objectData[$listName], $listFilter)
                : null
            ;
        }

        return $result;
    }

    /**
     * Filter an Object List Data Block to keep only given Fields
     *
     * @param null|array $objectData Object Data Block
     * @param array      $filters    Array of Fields Ids
     *
     * @return array
     */
    public static function filterListData(?array $objectData, array $filters = array()): array
    {
        $result = array();
        //====================================================================//
        // Safety Check => List Data is not Empty
        if (!is_array($objectData)) {
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
     * @return mixed Sorted Array
     */
    public static function normalize(&$input)
    {
        //==============================================================================
        // Convert ArrayObjects To Simple Array
        if ($input instanceof ArrayObject) {
            $input = $input->getArrayCopy();
            //==============================================================================
            // Normalize Contents
            self::normalize($input);
        } elseif (is_array($input)) {
            //==============================================================================
            // Normalize Array Contents
            foreach ($input as &$value) {
                self::normalize($value);
            }
        } elseif (is_bool($input)) {
            //==============================================================================
            // Normalize Bool as Strings
            $input = $input ? '1' : '0';
        } elseif (is_numeric($input)) {
            //==============================================================================
            // Normalize Numbers as Strings
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
    public static function sort(array &$inputArray): array
    {
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
