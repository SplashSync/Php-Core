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

namespace Splash\Tests\WsObjects;

use Exception;
use Splash\Client\Splash;
use Splash\Tests\Tools\ObjectsCase;

/**
 * Objects Test Suite - Object Reading Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O07GetTest extends ObjectsCase
{
    /**
     * @var array
     */
    private array $objectList = array();

    /**
     * @var array
     */
    private array $objectCount = array();

    /**
     * Test reading a Single Object Field from Module
     *
     * @dataProvider objectFieldsProvider
     *
     * @param string $testSequence
     * @param string $objectType
     * @param array  $field
     *
     * @throws Exception
     *
     * @return void
     */
    public function testGetSingleFieldFromModule(string $testSequence, string $objectType, array $field): void
    {
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Get next Available Object ID from Module
        $objectId = $this->getNextObjectId($objectType);
        //====================================================================//
        //   Get Readable Object Fields List
        $fields = $this->reduceFieldList(Splash::object($objectType)->fields(), true);
        //====================================================================//
        //   Execute Action Directly on Module
        $data = Splash::object($objectType)->get($objectId, $fields);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data, array($field), $objectId);
    }

    /**
     * Test reading a Single Object Field from Module
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
    public function testGetAllFieldsFromModule(string $testSequence, string $objectType): void
    {
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Get next Available Object ID from Module
        $objectId = $this->getNextObjectId($objectType);
        //====================================================================//
        //   Get Readable Object Fields List
        $fields = $this->reduceFieldList(Splash::object($objectType)->fields(), true);
        //====================================================================//
        //   Execute Action Directly on Module
        $data = Splash::object($objectType)->get($objectId, $fields);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data, Splash::object($objectType)->fields(), $objectId);
    }

    /**
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return void
     */
    public function testFromObjectsService(string $testSequence, string $objectType): void
    {
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Get next Available Object ID from Module
        $objectId = $this->getNextObjectId($objectType);
        //====================================================================//
        //   Get Readable Object Fields List
        $fields = $this->reduceFieldList(Splash::object($objectType)->fields(), true);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(
            SPL_S_OBJECTS,
            SPL_F_GET,
            __METHOD__,
            array( "type" => $objectType, "id" => $objectId, "fields" => $fields)
        );
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data, Splash::object($objectType)->fields(), $objectId);
    }

    /**
     * Test Reading Object from Service with Errors
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
    public function testFromObjectsServiceErrors(string $testSequence, string $objectType): void
    {
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_FIELDS, __METHOD__);
        //====================================================================//
        //      Request Reading without Sending ObjectID
        $this->genericErrorAction(
            SPL_S_OBJECTS,
            SPL_F_GET,
            __METHOD__,
            array( "type" => $objectType, "fields" => array())
        );
        //====================================================================//
        //      Request Reading but Sending NUll ObjectID
        $this->genericErrorAction(
            SPL_S_OBJECTS,
            SPL_F_GET,
            __METHOD__,
            array( "type" => $objectType, "id" => null, "fields" => array())
        );
        $this->genericErrorAction(
            SPL_S_OBJECTS,
            SPL_F_GET,
            __METHOD__,
            array( "type" => $objectType, "id" => 0, "fields" => array())
        );
    }

    /**
     * Get ID of Next Available Object
     *
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return string
     */
    public function getNextObjectId(string $objectType): string
    {
        //====================================================================//
        //   If Object List Not Loaded
        if (!isset($this->objectList[$objectType])) {
            //====================================================================//
            //   Get Object List from Module
            $list = Splash::object($objectType)->objectsList();

            //====================================================================//
            //   Get Object Count
            $this->objectCount[$objectType] = $list["meta"]["current"];

            //====================================================================//
            //   Remove Meta Data form Objects List
            unset($list["meta"]);

            //====================================================================//
            //   Convert ArrayObjects
            $this->objectList[$objectType] = $list;
        }

        //====================================================================//
        //   Verify Objects List is Not Empty
        if ($this->objectCount[$objectType] <= 0) {
            $this->markTestSkipped('No Objects in Database.');
        }

        //====================================================================//
        //   Return First Object of List
        $nextObject = array_shift($this->objectList[$objectType]);

        return $nextObject["id"];
    }

    /**
     * Verify Object Get Response
     *
     * @param null|array   $data
     * @param array        $fields
     * @param false|string $objectId
     *
     * @return void
     */
    public function verifyResponse(?array $data, array $fields, $objectId): void
    {
        //====================================================================//
        //   Verify Response Block
        $this->assertNotEmpty($data, "Data Block is Empty");
        $this->assertIsArray($data, "Data Block is Not an Array");
        //====================================================================//
        //   Verify Object Id is Present
        $this->assertArrayHasKey(
            "id",
            $data,
            "Object Identifier ['id'] is not defined in returned Data Block."
        );
        $this->assertEquals(
            $data["id"],
            $objectId,
            "Object Identifier ['id'] is different in returned Data Block."
        );
        //====================================================================//
        //  Verify Field Data
        foreach ($fields as $field) {
            //==============================================================================
            //      Filter Non-Readable Fields
            if (!$field['read']) {
                continue;
            }
            //==============================================================================
            //      Validate Field Data
            $this->isValidFieldData($data, $field['id'], $field['type']);
        }
    }
}
