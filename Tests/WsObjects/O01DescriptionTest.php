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
 * Objects Test Suite - Object Description Verifications
 */
class O01DescriptionTest extends ObjectsCase
{
    /**
     * Test Loading Object Description from Module
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
        //   Execute Action Directly on Module
        $data = Splash::object($objectType)->description();
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * Test Loading Object Description from Service
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
    public function testFromObjectsService(string $testSequence, string $objectType): void
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
     * Test Loading Object Description without ObjectType
     *
     * @return void
     */
    public function testFromObjectsServiceErrors(): void
    {
        //====================================================================//
        //      Request definition without Sending ObjectType
        $this->genericErrorAction(SPL_S_OBJECTS, SPL_F_DESC, __METHOD__);
    }

    /**
     * Verify Module Response
     *
     * @param array $data
     *
     * @return void
     */
    public function verifyResponse(array $data): void
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data, "Object Description is Empty");
        $this->assertIsArray($data, "Object Description is Not an Array");

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
