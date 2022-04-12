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
use Splash\Server\SplashServer;
use Splash\Tests\Tools\AbstractBaseCase;

/**
 * Admin Test Suite - Ping Client Verifications
 */
class A01PingTest extends AbstractBaseCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        //====================================================================//
        // BOOT or REBOOT MODULE
        Splash::reboot();
        //====================================================================//
        // Force Module to Use NuSOAP if Php SOAP Selected
        if ("SOAP" == Splash::configuration()->WsMethod) {
            Splash::configuration()->WsMethod = "NuSOAP";
        }
        //====================================================================//
        // FAKE SPLASH SERVER HOST URL
        Splash::configuration()->WsHost = $this->getLocalServerSoapUrl();
        Splash::ws()->setup();
    }

    /**
     * Test of Client Ping
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testPingClientAction(string $testSequence): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Check Test Mode Allow Server Ping
        if (!empty(Splash::input("SPLASH_TRAVIS"))) {
            //   Skip Test without Warnings
            $this->assertTrue(true);

            return;
        }

        //====================================================================//
        //   Execute Ping From Module to Splash Server
        $this->assertTrue(
            Splash::ping(),
            "Test of Splash Server Ping Fail. "
                ."Maybe this server is not connected? Check your configuration."
        );

        Splash::log()->cleanLog();
    }

    /**
     * Test of Server Ping
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testPingServerAction(string $testSequence): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Execute Ping From Splash Server to Module
        $response = SplashServer::ping();
        $data = Splash::ws()->unPack((string) $response, true);

        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($response, "Ping Response Block is Empty");
        $this->assertNotEmpty($data, "Ping Response Data is Empty");
        $this->assertIsArray($data, "Ping Response Data is Not an Array");
        $this->assertArrayHasKey("result", $data, "Ping Result is Missing");
        $this->assertNotEmpty($data["result"], "Ping Result is not True");
    }
}
