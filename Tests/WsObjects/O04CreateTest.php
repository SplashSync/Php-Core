<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
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
 * @abstract    Objects Test Suite - Object create Verification Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O04CreateTest extends ObjectsCase
{
    /**
     * Test Create an Object from Local Class
     *
     * @dataProvider objectTypesProvider
     *
     * @param mixed $testSequence
     * @param mixed $objectType
     */
    public function testFromModule($testSequence, $objectType)
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $dummyData = $this->prepareForTesting($objectType);
        if (false == $dummyData) {
            return true;
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
     * @param mixed $testSequence
     * @param mixed $objectType
     */
    public function testFromService($testSequence, $objectType)
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Generate Dummy Object Data (Required Fields Only)
        $dummyData = $this->prepareForTesting($objectType);
        if (false == $dummyData) {
            return true;
        }

        //====================================================================//
        //   Execute Action Directly on Module
        $objectId = $this->genericAction(
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
     * @return boolean
     */
    public function verifyTestIsAllowed($objectType)
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

    public function verifyResponse($objectType, $objectId)
    {
        //====================================================================//
        //   Verify Object Id Is Not Empty
        $this->assertNotEmpty($objectId, 'Returned New Object Id is Empty');

        //====================================================================//
        //   Add Object Id to Created List
        $this->addTestedObject($objectType, $objectId);

        //====================================================================//
        //   Verify Object Id Is in Right Format
        $this->assertTrue(
            is_integer($objectId) || is_string($objectId),
            'New Object Id is not an Integer or a Strings'
        );

        //====================================================================//
        //   Verify Object Change Was Commited
        $this->assertIsLastCommited(SPL_A_CREATE, $objectType, (string) $objectId);
    }
}
