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

namespace Splash\Tests\WsObjects;

use ArrayObject;
use Exception;
use Splash\Client\Splash;
use Splash\Tests\Tools\ObjectsCase;

/**
 * Objects Test Suite - Fields List Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O02FieldsTest extends ObjectsCase
{
    /**
     * Test Reading Object Fields from Local Class
     *
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return void
     */
    public function testFieldsFromModule(string $testSequence, string $objectType): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Execute Action Directly on Module
        $data = Splash::object($objectType)->fields();

        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * Test Reading Object Fields from Objects Service
     *
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return void
     */
    public function testFieldsFromObjectsService(string $testSequence, string $objectType): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(
            SPL_S_OBJECTS,
            SPL_F_FIELDS,
            __METHOD__,
            array( "id" => null, "type" => $objectType)
        );

        //====================================================================//
        //   Service May Return an ArrayObject (ArrayObject created by WebService)
        $this->assertInstanceOf("ArrayObject", $data, "Service Fields List is Not an ArrayObject");
        $data = $data->getArrayCopy();
        foreach ($data as $key => $field) {
            $this->assertInstanceOf("ArrayObject", $field, "Service Field is Not an ArrayObject");
            $data[$key] = $field->getArrayCopy();
        }

        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * Test Reading Object Fields Errors from Objects Service
     *
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testFieldsFromObjectsServiceErrors(string $testSequence): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__);
    }

    /**
     * Verify Client Response.
     *
     * @param array[]|bool|string $data
     *
     * @return void
     */
    public function verifyResponse($data): void
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data, "Object Fields List is Empty");
        $this->assertIsArray($data, "Object Fields List is Not an Array");

        //====================================================================//
        // All Fields Definitions are is right format
        //====================================================================//
        foreach ($data as $fieldData) {
            $this->verifyFieldRequired($fieldData);
            $this->verifyFieldMetaData($fieldData);
            $this->verifyFieldOptional($fieldData);
            $this->verifyFieldAssociations($fieldData, $data);
        }
    }

    /**
     * Verify Main Field Informations are in right format
     *
     * @param array $field
     *
     * @return void
     */
    public function verifyFieldRequired(array $field): void
    {
        //====================================================================//
        // Verify Field Type Name Exists
        $this->assertArrayInternalType($field, "type", "string", "Field Type");
        $this->assertNotEmpty(
            self::isValidType($field["type"]),
            "Field Type '".$field["type"]."' is not a Valid Splash Field Type."
        );

        //====================================================================//
        // Remove List Name if List Fields Type
        if (self::isListField($field["type"])) {
            $fieldListType = self::isListField($field["type"]);
            $this->assertIsArray($fieldListType);
            $fieldType = $fieldListType["fieldname"];
        } else {
            $fieldType = $field["type"];
        }

        //====================================================================//
        // If Field is Id Field => Verify The given Object Type Exists
        if (self::isValidType($fieldType) && self::isIdField($fieldType)) {
            $objectId = self::isIdField($fieldType);
            $this->assertIsArray($objectId);
            $this->assertTrue(
                in_array($objectId["ObjectType"], Splash::objects(), true),
                "Object ID Field of Type '".$objectId["ObjectType"]."' is not a Valid. "
                    ."This Object Type was not found."
            );
        }

        //====================================================================//
        // All Required Informations are Available and is right format
        $this->assertArrayInternalType($field, "id", "string", "Field Identifier");
        $this->assertArrayInternalType($field, "name", "string", "Field Name");
        $this->assertArrayInternalType($field, "desc", "string", "Field Description");
        $this->assertArraySplashBool($field, "required", "Field Required Flag");
        $this->assertArraySplashBool($field, "write", "Field Write Flag");
        $this->assertArraySplashBool($field, "read", "Field Read Flag");
        $this->assertArraySplashBool($field, "inlist", "Field In List Flag");
        $this->assertArraySplashArray($field, "choices", "Field Possible Values [key => xxx, value => yyy] ");
        $this->assertArraySplashArray($field, "options", "Field Faker Options [key => value]");
    }

    /**
     * @param array $field
     *
     * @return void
     */
    public function verifyFieldMetaData(array $field): void
    {
        //====================================================================//
        // Field MicroData Infos
        if (isset($field["itemtype"]) && !empty($field["itemtype"])) {
            $this->assertArrayInternalType($field, "itemtype", "string", "Field MicroData URL");
            $this->assertArrayInternalType($field, "itemprop", "string", "Field MicroData Property");
        }

        //====================================================================//
        // Field Tag
        if (isset($field["tag"]) && !empty($field["tag"])) {
            $this->assertArrayInternalType($field, "tag", "string", "Field Linking Tag");
        }
        if (isset($field["tag"], $field["itemtype"]) && !empty($field["itemtype"])) {
            $this->assertEquals(
                $field["tag"],
                md5($field["itemprop"].IDSPLIT.$field["itemtype"]),
                "Field Tag do not match with defined MicroData. Expected Format: md5('itemprop'@'itemptype') "
            );
        }
    }

    /**
     * @param array $field
     *
     * @return void
     */
    public function verifyFieldOptional(array $field): void
    {
        //====================================================================//
        // Field Format
        if (isset($field["format"])) {
            $this->assertArrayInternalType($field, "format", "string", "Field Format Description");
        }
        //====================================================================//
        // Field No Test Flag
        if (isset($field["notest"])) {
            $this->assertArraySplashBool($field, "notest", "Field NoTest Flag");
        }
    }

    /**
     * @param array   $field
     * @param array[] $fields
     *
     * @return void
     */
    public function verifyFieldAssociations(array $field, array $fields): void
    {
        if (!isset($field["asso"]) || empty($field["asso"])) {
            return;
        }
        //====================================================================//
        // Field Associated Fields List
        foreach ($field["asso"] as $fieldType) {
            //====================================================================//
            // Check FieldType Name
            $this->assertIsString($fieldType, "Associated FieldType must be String Format");

            //====================================================================//
            // Check FieldType Exists
            $assoField = null;
            foreach ($fields as $item) {
                if ($item["id"] === $fieldType) {
                    $assoField = $item;
                }
            }
            $this->assertNotEmpty($assoField, "Associated Field ".$fieldType." isn't an existing Field Id String.");
        }
    }
}
