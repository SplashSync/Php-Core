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
use Splash\Components\CommitsManager;
use Splash\Tests\Tools\ObjectsCase;

/**
 * Objects Test Suite - Fields List Verifications
 */
class O05DeleteTest extends ObjectsCase
{
    /**
     * Test Delete Object from Module
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
    public function testFromModule(string $testSequence, string $objectType): void
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
        // Clean Objects Committed Array
        CommitsManager::reset();
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
     * Test Delete Object from Service
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
    public function testFromService(string $testSequence, string $objectType): void
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
        // Clean Objects Committed Array
        CommitsManager::reset();
        //====================================================================//
        //   Execute Action Directly on Module
        $data = $this->genericStringAction(
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
     * Test Delete Object with Inputs Errors
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
     * @throws Exception
     *
     * @return bool
     */
    public function verifyTestIsAllowed(string $objectType): bool
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
     * @throws Exception
     *
     * @return null|array
     */
    public function prepareForTesting(string $objectType): ?array
    {
        //====================================================================//
        //   Verify Test is Required
        if (!$this->verifyTestIsAllowed($objectType)) {
            return null;
        }

        //====================================================================//
        // Read Required Fields & Prepare Dummy Data
        //====================================================================//
        $write = false;
        $fields = Splash::object($objectType)->fields();
        foreach ($fields as $key => $field) {
            //====================================================================//
            // Skip Non Required Fields
            if (!$field["required"]) {
                unset($fields[$key]);
            }
            //====================================================================//
            // Check if Write Fields
            if ($field["write"]) {
                $write = true;
            }
        }

        //====================================================================//
        // If No Writable Fields
        if (!$write) {
            return null;
        }

        //====================================================================//
        // Lock New Objects To Avoid Action Commit
        Splash::object($objectType)->lock();

        //====================================================================//
        // Clean Objects Committed Array
        CommitsManager::reset();

        return $this->fakeObjectData($fields);
    }

    /**
     * @param string $objectType
     * @param mixed  $objectId
     *
     * @return void
     */
    public function verifyCreateResponse(string $objectType, $objectId): void
    {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($objectId, "Returned New Object Id is Empty");
        $this->assertIsString($objectId, "Returned New Object Id is Not a String");
        //====================================================================//
        //   Add Object Id to Created List
        $this->addTestedObject($objectType, (string) $objectId);
    }

    /**
     * Verify Delete Object Response
     *
     * @param string       $objectType
     * @param null|string  $objectId
     * @param array|scalar $data
     *
     * @throws Exception
     *
     * @return void
     */
    public function verifyDeleteResponse(string $objectType, ?string $objectId, $data): void
    {
        //====================================================================//
        //   Verify Response
        $this->assertIsSplashBool($data, "Object Delete Response Must be a Bool");
        $this->assertNotEmpty($data, "Object Delete Response is Not True");
        //====================================================================//
        //   Verify Object Change Was Committed
        $this->assertIsLastCommitted(SPL_A_DELETE, $objectType, (string) $objectId);
        //====================================================================//
        //   Verify Repeating Delete as Same Result
        $repeatedResponse = Splash::object($objectType)->delete((string) $objectId);
        $this->assertTrue(
            $repeatedResponse,
            "Object Repeated Delete, Must return True even if Object Already Deleted."
        );
        //====================================================================//
        //   Verify Object not Present anymore
        $fields = $this->reduceFieldList(Splash::object($objectType)->fields(), true);
        $getResponse = Splash::object($objectType)->get((string) $objectId, $fields);
        $this->assertNull($getResponse, "Object Not Delete, I can still read it!!");
    }
}
