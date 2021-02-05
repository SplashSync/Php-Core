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
 * Objects Test Suite - Object Description Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class O01DescriptionTest extends ObjectsCase
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
        //   Execute Action Directly on Module
        $data = Splash::object($objectType)->description();
        //====================================================================//
        //   Module May Return an Array (ArrayObject created by WebService)
        if (is_array($data)) {
            $data = new ArrayObject($data);
        }
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * @dataProvider objectTypesProvider
     *
     * @param string $testSequence
     * @param string $objectType
     *
     * @return void
     */
    public function testFromObjectsService($testSequence, $objectType)
    {
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(
            SPL_S_OBJECTS,
            SPL_F_DESC,
            __METHOD__,
            array( "id" => null, "type" => $objectType)
        );
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * @return void
     */
    public function testFromObjectsServiceErrors()
    {
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_DESC, __METHOD__);
    }

    /**
     * @param ArrayObject|bool|string $data
     *
     * @return void
     */
    public function verifyResponse($data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data, "Object Description is Empty");
        $this->assertInstanceOf("ArrayObject", $data, "Object Description is Not an ArrayObject");

        //====================================================================//
        // All Informations are Available and is right format
        //====================================================================//

        //====================================================================//
        // General Object definition
        $this->assertNotEmpty($data["type"], "Object Type is Empty");
        $this->assertIsString($data["type"], "Object Type is Not a String.");
        $this->assertNotEmpty($data["name"], "Object Name is Empty");
        $this->assertIsString($data["name"], "Object Name is Not a String.");
        $this->assertNotEmpty($data["description"], "Object Description is Empty");
        $this->assertIsString($data["description"], "Object Description is Not a String.");
        $this->assertIsSplashBool($data["disabled"], "Object Disabled Flag is Not a Bool.");

        //====================================================================//
        // Object Limitations
        $this->assertIsSplashBool($data["allow_push_created"], "Allow Push Created Flag is Not a Bool.");
        $this->assertIsSplashBool($data["allow_push_updated"], "Allow Push Updated Flag is Not a Bool.");
        $this->assertIsSplashBool($data["allow_push_deleted"], "Allow Push Deleted Flag is Not a Bool.");

        //====================================================================//
        // Object Default Configuration
        $this->assertIsSplashBool($data["enable_push_created"], "Enable Push Created Flag is Not a Bool.");
        $this->assertIsSplashBool($data["enable_push_updated"], "Enable Push Updated Flag is Not a Bool.");
        $this->assertIsSplashBool($data["enable_push_deleted"], "Enable Push Deleted Flag is Not a Bool.");
        $this->assertIsSplashBool($data["enable_pull_created"], "Enable Pull Created Flag is Not a Bool.");
        $this->assertIsSplashBool($data["enable_pull_updated"], "Enable Pull Updated Flag is Not a Bool.");
        $this->assertIsSplashBool($data["enable_pull_deleted"], "Enable Pull Deleted Flag is Not a Bool.");
    }
}
