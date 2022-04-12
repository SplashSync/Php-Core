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
 * Objects Test Suite - Object create Verification Verifications
 */
class O04CreateTest extends ObjectsCase
{
    /**
     * Test Create an Object from Local Class
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
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $dummyData = $this->prepareForTesting($objectType);
        if (!$dummyData) {
            return;
        }
        //====================================================================//
        //   Execute Action Directly on Module
        $objectId = Splash::object($objectType)->set(null, $dummyData);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($objectType, $objectId);
    }

    /**
     * Test Create an Object from Object Service
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
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $dummyData = $this->prepareForTesting($objectType);
        if (false == $dummyData) {
            return;
        }
        //====================================================================//
        //   Execute Action Directly on Module
        $objectId = $this->genericStringAction(
            SPL_S_OBJECTS,
            SPL_F_SET,
            __METHOD__,
            array('id' => null, 'type' => $objectType, 'fields' => $dummyData)
        );
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($objectType, $objectId);
    }

    /**
     * Verify Test Allowed for this Object Type
     *
     * @param string $objectType
     *
     * @throws Exception
     *
     * @return bool
     */
    public function verifyTestIsAllowed(string $objectType): bool
    {
        $definition = Splash::object($objectType)->description();

        //====================================================================//
        //   Verify Create is Allowed
        if ($definition['allow_push_created']) {
            return true;
        }
        $this->assertTrue(true, 'Object Creation not Allowed, Test Skipped.');

        return false;
    }

    /**
     * Prepare Fake Object Dataset for Test
     *
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
     * Verify Create Object Response
     *
     * @param string $objectType
     * @param mixed  $objectId
     *
     * @return void
     */
    public function verifyResponse(string $objectType, $objectId): void
    {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($objectId, 'Returned New Object Id is Empty');
        $this->assertIsString($objectId, 'Returned New Object Id is not a String');
        //====================================================================//
        //   Add Object Id to Created List
        $this->addTestedObject($objectType, (string) $objectId);
        //====================================================================//
        //   Verify Object Change Was Committed
        $this->assertIsLastCommitted(SPL_A_CREATE, $objectType, (string) $objectId);
    }
}
