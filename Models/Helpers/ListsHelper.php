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

namespace   Splash\Models\Helpers;

use Splash\Models\Fields\FieldsManagerTrait;

/**
 * Helper for List Fields Management
 */
class ListsHelper
{
    use FieldsManagerTrait;

    //====================================================================//
    // FIELDS LIST IDENTIFIERS MANAGEMENT
    //====================================================================//

    /**
     * Create a List Field Identifier String
     *
     * @param string $listName  Field List Name.
     * @param string $fieldName Field Identifier
     *
     * @return null|string
     */
    public function encode(string $listName, string $fieldName): ?string
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
        // Create & Return List Field Id Data String
        return   $fieldName.LISTSPLIT.$listName;
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
    public function initOutput(array &$buffer, string $listName, string $fieldName): ?string
    {
        //====================================================================//
        // Check List Name
        if ($this->listName($fieldName) !== $listName) {
            return null;
        }
        //====================================================================//
        // Create List Array If Needed
        if (!array_key_exists($listName, $buffer)) {
            $buffer[$listName] = array();
        }
        //====================================================================//
        // decode Field Name
        return $this->fieldName($fieldName);
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
    public function insert(array &$buffer, string $listName, string $fieldName, $key, $itemData): void
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
        $fieldIndex = explode(LISTSPLIT, $fieldName);
        $buffer[$listName][$key][$fieldIndex[0]] = $itemData;
    }
}
