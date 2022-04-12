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

namespace Splash\Tests\WsAdmin;

use Exception;
use Splash\Client\Splash;
use Splash\Tests\Tools\AbstractBaseCase;

/**
 * Admin Test Suite - Get Objects List Client Verifications
 */
class A03ObjectsTest extends AbstractBaseCase
{
    /**
     * Test Loading Objects List from Local Class
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testObjectsFromClass(string $testSequence): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Execute Action From Module
        $data = Splash::objects();
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * Test Loading Objects List from Admin Service
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testObjectsFromAdminService(string $testSequence): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_ADMIN, SPL_F_GET_OBJECTS, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * Test Loading Objects List from Admin Service
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testObjectsFromObjectsService(string $testSequence): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_OBJECTS, SPL_F_OBJECTS, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * Verify Client Response.
     *
     * @param array $data
     *
     * @return void
     */
    private function verifyResponse(array $data): void
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data, "Objects List is Empty");
        $this->assertIsArray($data, "Objects List is Not an Array");
        //====================================================================//
        // CHECK ITEMS
        foreach ($data as $objectType) {
            $this->assertNotEmpty($objectType, "Objects Type is Empty");
            $this->assertIsString(
                $objectType,
                "Objects Type is Not an String. (Given".print_r($objectType, true).")"
            );
        }
    }
}
