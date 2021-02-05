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
    private $objectList = array();

    /**
     * @var array
     */
    private $objectCount = array();

    /**
     * @dataProvider objectFieldsProvider
     *
     * @param string $testSequence
     * @param string $objectType
     * @param mixed  $field
     *
     * @return void
     */
    public function testGetSingleFieldFromModule($testSequence, $objectType, $field)
    {
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Get next Available Object ID from Module
        $objectId = $this->getNextObjectId($objectType);

        //====================================================================//
        //   Get Readable Object Fields List
        $fields = $this->reduceFieldList(Splash::object($objectType)->fields(), true, false);

        //====================================================================//
        //   Execute Action Directly on Module
        $data = Splash::object($objectType)->get($objectId, $fields);

        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($data)) {
            $data = new ArrayObject($data);
        }

        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data, array($field), $objectId);
    }

    /**
     * @dataProvider objectTypesProvider
     *
     * @param mixed $testSequence
     * @param mixed $objectType
     *
     * @return void
     */
    public function testGetAllFieldsFromModule($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Get next Available Object ID from Module
        $objectId = $this->getNextObjectId($objectType);

        //====================================================================//
        //   Get Readable Object Fields List
        $fields = $this->reduceFieldList(Splash::object($objectType)->fields(), true, false);

        //====================================================================//
        //   Execute Action Directly on Module
        $data = Splash::object($objectType)->get($objectId, $fields);

        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($data)) {
            $data = new ArrayObject($data);
        }

        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data, Splash::object($objectType)->fields(), $objectId);
    }

    /**
     * @dataProvider objectTypesProvider
     *
     * @param mixed $testSequence
     * @param mixed $objectType
     *
     * @return void
     */
    public function testFromObjectsService($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Get next Available Object ID from Module
        $objectId = $this->getNextObjectId($objectType);

        //====================================================================//
        //   Get Readable Object Fields List
        $fields = $this->reduceFieldList(Splash::object($objectType)->fields(), true, false);

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
     * @dataProvider objectTypesProvider
     *
     * @param mixed $testSequence
     * @param mixed $objectType
     *
     * @return void
     */
    public function testFromObjectsServiceErrors($testSequence, $objectType)
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
     * @param string $objectType
     *
     * @return string
     */
    public function getNextObjectId($objectType)
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
            //   Remove Meta Datats form Objects List
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
     * @param mixed        $data
     * @param array        $fields
     * @param false|string $objectId
     *
     * @return void
     */
    public function verifyResponse($data, $fields, $objectId)
    {
        //====================================================================//
        //   Verify Response Block
        $this->assertNotEmpty($data, "Data Block is Empty");
        $this->assertInstanceOf("ArrayObject", $data, "Data Block is Not an ArrayObject");

        //====================================================================//
        //   Verify Object Id is Present
        $this->assertArrayHasKey("id", $data, "Object Identifier ['id'] is not defined in returned Data Block.");
        $this->assertEquals($data["id"], $objectId, "Object Identifier ['id'] is different in returned Data Block.");

        //====================================================================//
        //  Verify Field Data
        foreach ($fields as $field) {
            //==============================================================================
            //      Filter Non-Readable Fields
            if (!$field->read) {
                continue;
            }
            //==============================================================================
            //      Validate Field Data
            $this->isValidFieldData($data, $field->id, $field->type);
        }
    }
}
