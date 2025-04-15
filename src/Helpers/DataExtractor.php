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

use ArrayObject;

/**
 * Extract Fields Data from Object Data Block
 */
class DataExtractor
{
    /**
     * Extract Single Field Data from an Object Data Block
     *
     * @param array       $objectData Object Data Block
     * @param null|string $filter     Single Fields Id
     *
     * @return null|array|scalar
     */
    public static function extractField(array $objectData, ?string $filter)
    {
        //====================================================================//
        // Safety Check
        if (empty($filter)) {
            return null;
        }
        //====================================================================//
        // Filter Object Data on a Single Field
        $filteredData = self::filterData($objectData, array($filter));

        //====================================================================//
        // Simple Single Field Extraction
        //====================================================================//
        if (!$listName = ListsHelper::listName($filter)) {
            return $filteredData[$filter] ?? null;
        }

        //====================================================================//
        // List Field
        //====================================================================//

        //====================================================================//
        // Check List Exists
        $listData = $filteredData[$listName] ?? null;
        $fieldName = ListsHelper::fieldName($filter);
        if (!$fieldName || !is_iterable($listData)) {
            return null;
        }
        //====================================================================//
        // Parse Raw List Data
        $result = array();
        foreach ($listData as $key => $item) {
            $result[$key] = $item[$fieldName] ?? null;
        }

        return $result;
    }

    /**
     * Filter an Object Data Block to keep only given Fields
     *
     * @param array    $objectData Object Data Block
     * @param string[] $fieldIds   Array of Fields Ids
     *
     * @return null|array
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public static function filterData(array $objectData, array $fieldIds): ?array
    {
        $result = array();
        //====================================================================//
        // Walk on Requested Fields Ids
        foreach ($fieldIds as $fieldId) {
            //====================================================================//
            // Single Field Data Type
            if (!$listName = ListsHelper::listName($fieldId)) {
                $result = array_replace_recursive(
                    $result,
                    self::filterOnSimpleField($objectData, $fieldId)
                );

                continue;
            }
            //====================================================================//
            // List Field Data Type
            if ($fieldName = ListsHelper::fieldName($fieldId)) {
                $result = array_replace_recursive(
                    $result,
                    self::filterOnListField($objectData, $listName, $fieldName)
                );
            }
        }

        return $result;
    }

    /**
     * Extract Simple Field Data from an Object Data Block
     *
     * @param array  $objectData Object Data Block
     * @param string $fieldId    Name of Field
     *
     * @return array<string, null|array|scalar>
     */
    private static function filterOnSimpleField(array $objectData, string $fieldId): array
    {
        if (array_key_exists($fieldId, $objectData)) {
            return array(
                $fieldId => $objectData[$fieldId]
            );
        }

        return array();
    }

    /**
     * Extract List Field Data from an Object Data Block
     *
     * @param array  $objectData Object Data Block
     * @param string $listName   Name of List
     * @param string $fieldId    Name of Field
     *
     * @return array<string, array<array<string, null|array|scalar>>>
     */
    private static function filterOnListField(array $objectData, string $listName, string $fieldId): array
    {
        $fieldData = array();
        //====================================================================//
        // Walk on List Items
        $listData = $objectData[$listName] ?? null;
        if (is_iterable($listData)) {
            foreach ($listData as $index => $listItem) {
                $fieldData[$index] ??= array();
                //====================================================================//
                // Convert ArrayObjects to Array
                if ($listItem instanceof ArrayObject) {
                    $listItem = $listItem->getArrayCopy();
                }
                //====================================================================//
                // Ensure Item is An Array of Fields
                if (!is_array($listItem)) {
                    continue;
                }
                //====================================================================//
                // Extract Item Data for Field
                if (array_key_exists($fieldId, $listItem)) {
                    $fieldData[$index][$fieldId] = $listItem[$fieldId];
                }
            }
        }
        //====================================================================//
        // Extract Item Data for Field
        if (empty(array_filter($fieldData))) {
            return array();
        }

        return array(
            $listName => $fieldData
        );
    }
}
