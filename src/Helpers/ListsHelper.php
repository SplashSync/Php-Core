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
 * Helper for List Fields Management
 */
class ListsHelper
{
    /**
     * List Fields Type Splitter
     */
    const SPLIT = '@';

    /**
     * List Name Prop
     */
    const LIST_NAME = 'listName';

    /**
     * Field Name Prop
     */
    const FIELD_NAME = 'fieldName';

    //====================================================================//
    // FIELDS LIST IDENTIFIERS MANAGEMENT
    //====================================================================//

    /**
     * Create a List Field Identifier String
     *
     * @param string $listName  Field List Name.
     * @param string $fieldName Field Identifier
     *
     * @phpstan-return ($listName is non-empty-string ? ($fieldName is non-empty-string ? string : null) : null)
     *
     * @return null|string
     */
    public static function encode(string $listName, string $fieldName): ?string
    {
        //====================================================================//
        // Safety Checks
        if (empty($listName)) {
            return null;
        }
        if (empty($fieldName)) {
            return null;
        }

        //====================================================================//
        // Create & Return List Field ID Data String
        return $fieldName.self::SPLIT.$listName;
    }

    /**
     * Check if a Field ID or Type is a List Identifier
     *
     * @param null|string $fieldType Data Type Name String
     *
     * @return bool
     */
    public static function isList(?string $fieldType): bool
    {
        return is_array(self::explode($fieldType));
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
        return self::explode($listFieldName)[self::FIELD_NAME] ?? null;
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
        return self::explode($listFieldName)[self::LIST_NAME] ?? null;
    }

    //====================================================================//
    // FIELDS LIST DATA MANAGEMENT
    //====================================================================//

    /**
     * Validate & Init List before Adding Data
     *
     * @param array  $buffer    Object Data Buffer
     * @param string $listName  List Identifier String
     * @param string $fieldName List Field Identifier String
     *
     * @return null|string
     */
    public static function initOutput(array &$buffer, string $listName, string $fieldName): ?string
    {
        //====================================================================//
        // Check List Name
        if (self::listName($fieldName) !== $listName) {
            return null;
        }
        //====================================================================//
        // Create List Array If Needed
        if (!array_key_exists($listName, $buffer)) {
            $buffer[$listName] = array();
        }

        //====================================================================//
        // decode Field Name
        return self::fieldName($fieldName);
    }

    /**
     * Add Item Data in Given  Output List
     *
     * @param array      $buffer    Object Data Buffer
     * @param string     $listName  List Identifier String
     * @param string     $fieldName List Field Identifier String
     * @param int|string $key       List Item Index Key
     * @param mixed      $itemData  Item Data
     *
     * @return void
     */
    public static function insert(array &$buffer, string $listName, string $fieldName, $key, $itemData): void
    {
        //====================================================================//
        // Create List Array If Needed
        if (!array_key_exists($listName, $buffer)) {
            $buffer[$listName] = array();
        }
        //====================================================================//
        // Create Line Array If Needed
        if (!array_key_exists($key, $buffer[$listName])) {
            $buffer[$listName][$key] = array();
        }
        //====================================================================//
        // Store Data in Array
        $fieldIndex = explode(self::SPLIT, $fieldName);
        $buffer[$listName][$key][$fieldIndex[0]] = $itemData;
    }

    /**
     * Check if a Field ID or Type is a List Identifier
     *
     * @param null|string $fieldType Data Type Name String
     *
     * @return null|array<string, string> Exploded List field Array or Null
     */
    private static function explode(?string $fieldType): ?array
    {
        //====================================================================//
        // Safety Check
        if (empty($fieldType)) {
            return null;
        }
        //====================================================================//
        // Detects Lists
        $list = explode(self::SPLIT, $fieldType);
        if (is_array($list) && (2 == count($list))) {
            //====================================================================//
            // If List Detected, Prepare Field List Information Array
            return array(
                self::FIELD_NAME => $list[0],
                self::LIST_NAME => $list[1]
            );
        }

        return null;
    }
}
