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

use Splash\Client\Splash;
use Splash\Tests\Tools\ObjectsCase;

/**
 * Objects Test Suite - Fields List Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O05DeleteTest extends ObjectsCase
{
    /**
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @return void
     */
    public function testFromModule($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $dummyData = $this->prepareForTesting($objectType);
        if (false == $dummyData) {
            return;
        }

        //====================================================================//
        //   Create a New Object on Module
        $objectId = Splash::object($objectType)->set(null, $dummyData);

        //====================================================================//
        //   Verify Response
        $this->verifyCreateResponse($objectType, $objectId);

        //====================================================================//
        // Clean Objects Commited Array
        Splash::$commited = array();
        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($objectType)->lock((string) $objectId);

        //====================================================================//
        //   Delete Object on Module
        $data = Splash::object($objectType)->delete((string) $objectId);

        //====================================================================//
        //   Verify Response
        $this->verifyDeleteResponse($objectType, $objectId, $data);
    }

    /**
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @return void
     */
    public function testFromService($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $dummyData = $this->prepareForTesting($objectType);
        if (false == $dummyData) {
            return;
        }

        //====================================================================//
        //   Create a New Object on Module
        $objectId = Splash::object($objectType)->set(null, $dummyData);

        //====================================================================//
        //   Verify Response
        $this->verifyCreateResponse($objectType, $objectId);

        //====================================================================//
        // Clean Objects Commited Array
        Splash::$commited = array();
        //====================================================================//
        //   Execute Action Directly on Module
        $data = $this->genericAction(
            SPL_S_OBJECTS,
            SPL_F_DEL,
            __METHOD__,
            array( "id" => $objectId, "type" => $objectType)
        );

        //====================================================================//
        //   Verify Response
        $this->verifyDeleteResponse($objectType, $objectId, $data);
    }

    /**
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @return void
     */
    public function testFromObjectsServiceErrors($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //      Request definition without Sending Parameters
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, array());
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, array( "id" => null ));
        //====================================================================//
        //      Request definition without Sending ObjectId
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_GET, __METHOD__, array( "type" => $objectType));
    }

    /**
     * @param string $objectType
     *
     * @return boolean
     */
    public function verifyTestIsAllowed($objectType)
    {
        $definition = Splash::object($objectType)->description();

        $this->assertNotEmpty($definition);
        //====================================================================//
        //   Verify Create is Allowed
        if (!$definition["allow_push_created"]) {
            return false;
        }
        //====================================================================//
        //   Verify Delete is Allowed
        if (!$definition["allow_push_deleted"]) {
            return false;
        }

        return true;
    }

    /**
     * @param string $objectType
     *
     * @return array|false
     */
    public function prepareForTesting($objectType)
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($objectType)) {
            return false;
        }

        //====================================================================//
        // Read Required Fields & Prepare Dummy Data
        //====================================================================//
        $write = false;
        $fields = Splash::object($objectType)->fields();
        foreach ($fields as $key => $field) {
            //====================================================================//
            // Skip Non Required Fields
            if (!$field->required) {
                unset($fields[$key]);
            }
            //====================================================================//
            // Check if Write Fields
            if ($field->write) {
                $write = true;
            }
        }

        //====================================================================//
        // If No Writable Fields
        if (!$write) {
            return false;
        }

        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($objectType)->lock();

        //====================================================================//
        // Clean Objects Commited Array
        Splash::$commited = array();

        return $this->fakeObjectData($fields);
    }

    /**
     * @param string           $objectType
     * @param false|int|string $objectId
     *
     * @return void
     */
    public function verifyCreateResponse($objectType, $objectId)
    {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($objectId, "Returned New Object Id is Empty");
        $this->assertIsScalar($objectId, "Returned New Object Id is Not a Scalar");

        //====================================================================//
        //   Add Object Id to Created List
        $this->addTestedObject($objectType, (string) $objectId);

        //====================================================================//
        //   Verify Object Id Is in Right Format
        $this->assertTrue(
            is_integer($objectId) || is_string($objectId),
            "New Object Id is not an Integer or a Strings"
        );
    }

    /**
     * @param string       $objectType
     * @param false|string $objectId
     * @param mixed        $data
     *
     * @return void
     */
    public function verifyDeleteResponse($objectType, $objectId, $data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertIsSplashBool($data, "Object Delete Response Must be a Bool");
        $this->assertNotEmpty($data, "Object Delete Response is Not True");

        //====================================================================//
        //   Verify Object Change Was Commited
        $this->assertIsLastCommited(SPL_A_DELETE, $objectType, (string) $objectId);

        //====================================================================//
        //   Verify Repeating Delete as Same Result
        $repeatedResponse = Splash::object($objectType)->delete((string) $objectId);
        $this->assertTrue(
            $repeatedResponse,
            "Object Repeated Delete, Must return True even if Object Already Deleted."
        );

        //====================================================================//
        //   Verify Object not Present anymore
        $fields = $this->reduceFieldList(Splash::object($objectType)->fields(), true, false);
        $getResponse = Splash::object($objectType)->get((string) $objectId, $fields);
        $this->assertFalse($getResponse, "Object Not Delete, I can still read it!!");
    }
}
