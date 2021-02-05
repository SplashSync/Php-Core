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

namespace Splash\Tests\Tools\Traits;

use Splash\Models\Fields\FieldsManagerTrait;

/**
 * Splash Test Tools - Objects Data Validation
 *
 * @author SplashSync <contact@splashsync.com>
 */
trait ObjectsValidatorTrait
{
    use FieldsManagerTrait;

    /**
     * Fields Classes Name Prefix
     *
     * @var string
     */
    protected static $CLASS_PREFIX = 'Splash\Tests\Tools\Fields\Oo';

    //==============================================================================
    //      VALIDATION FUNCTIONS
    //==============================================================================

    /**
     * Verify this parameter is a valid sync data type
     *
     * @param string $fieldType Data Type Name String
     *
     * @return false|string
     */
    public static function isValidType($fieldType)
    {
        //====================================================================//
        // Safety Check
        if (empty($fieldType)) {
            return false;
        }
        //====================================================================//
        // Detects Lists Fields
        //====================================================================//
        $list = self::isListField($fieldType);
        if (false != $list) {
            $fieldType = $list["fieldname"];
        }
        //====================================================================//
        // Detects Id Fields
        //====================================================================//
        $id = self::isIdField($fieldType);
        if (false != $id) {
            $fieldType = "objectid";
        }

        //====================================================================//
        // Verify Single Data Type is Valid
        //====================================================================//

        //====================================================================//
        // Build Class Full Name
        $className = self::$CLASS_PREFIX.$fieldType;

        //====================================================================//
        // Build New Entity
        if (class_exists($className)) {
            return  $className;
        }

        return false;
    }

    /**
     * Verify Data a valid Raw field data
     *
     * @param mixed  $data      Object Field Data
     * @param string $fieldType Object Field Type
     *
     * @return bool
     */
    public static function isValidData($data, $fieldType)
    {
        //====================================================================//
        // Verify Field Type is Valid
        $className = self::isValidType($fieldType);
        if (false == $className) {
            return false;
        }

        //====================================================================//
        // Verify Single Field Data Type is not Null
        if (is_null($data)) {
            return true;
        }

        //====================================================================//
        // Verify Single Field Data Type is Valid
        return $className::validate($data);
    }

    /**
     * Verify Data a valid field data
     *
     * @param mixed  $data      Object Field Data
     * @param string $fieldId   Object Field Identifier
     * @param string $fieldType Object Field Type
     *
     * @return bool
     */
    public function isValidFieldData($data, $fieldId, $fieldType)
    {
        //====================================================================//
        // Safety Check
        $this->assertNotEmpty($data, "Field Data Block is Empty");
        $this->assertNotEmpty($fieldId, "Field Id is Empty");
        $this->assertNotEmpty($fieldType, "Field Type Name is Empty");

        //====================================================================//
        // Detects Lists Fields
        $isList = self::isListField($fieldId);
        if ($isList) {
            //====================================================================//
            // Verify List Field Data
            return $this->isValidListFieldData($data, $fieldId, $fieldType);
        }

        //====================================================================//
        // Verify Field is in Data Response
        $this->assertArrayHasKey($fieldId, $data, "Field '".$fieldId."' is not defined in returned Data Block.");

        //====================================================================//
        // Verify Single Field Data Type is not Null
        if (is_null($data[$fieldId])) {
            return false;
        }

        //====================================================================//
        // Verify Raw Field Data
        $this->assertTrue(
            self::isValidData($data[$fieldId], $fieldType),
            $fieldId." => Field Raw Data is not a valid ".$fieldType.". (".print_r($data[$fieldId], true).")"
        );

        return true;
    }

    /**
     * Verify Data a valid list field data
     *
     * @param mixed  $data      Object Field Data
     * @param string $fieldId   Object Field Identifier
     * @param string $fieldType Object Field Type
     *
     * @return bool
     */
    public function isValidListFieldData($data, $fieldId, $fieldType)
    {
        $listId = self::isListField($fieldId);
        $listType = self::isListField($fieldType);
        if (!$listId) {
            return false;
        }

        //====================================================================//
        // Verify List is in Data Response
        $this->assertIsArray($listId);
        $this->assertArrayHasKey(
            $listId["listname"],
            $data,
            "List '".$listId["listname"]."' is not defined in returned Data Block."
        );

        //====================================================================//
        // Verify Field Type is List Type Identifier
        $this->assertIsArray($listType);
        $this->assertEquals(
            $listType["listname"],
            SPL_T_LIST,
            "List Field Type Must match Format 'type'@list. (Given ".print_r($fieldType, true).")"
        );

        //====================================================================//
        // Verify Field Type is Valid Splahs Field type
        $this->assertNotEmpty(
            self::isValidType($listType["fieldname"]),
            "List Field Type is not a valid Splash Field Type. (Given ".print_r($listType["fieldname"], true).")"
        );

        $listData = $data[$listId["listname"]];
        //====================================================================//
        // Verify if Field Data is Null
        if (empty($listData)) {
            return true;
        }

        //====================================================================//
        // Verify if Field Data is an Array
        $this->assertTrue(
            is_array($listData) || is_a($listData, "ArrayObject"),
            "List Field '".$listId["listname"]."' is not of Array Type. (Given ".print_r($listData, true).")"
        );

        //====================================================================//
        // Verify all List Data Are Valid
        foreach ($listData as $listValue) {
            $this->isValidFieldData($listValue, $listId["fieldname"], $fieldType);
        }

        return true;
    }
}
