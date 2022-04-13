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
 * Admin Test Suite - Connect Client Verifications
 */
class A02ConnectTest extends AbstractBaseCase
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
        // FAKE SPLASH SERVER HOST URL
        Splash::configuration()->WsHost = $this->getLocalServerSoapUrl();
        Splash::ws()->setup();
    }

    /**
     * Test of Client Connect
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testConnectClientAction(string $testSequence): void
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
        //   Execute Connect From Module to Splash Server
        $this->assertTrue(
            Splash::connect(),
            "Test of Splash Server Connect Fail. "
                ."Maybe this server is not connected? Check your configuration."
        );
        Splash::log()->cleanLog();
    }

    /**
     * Test of Server Connect
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testConnectServerAction(string $testSequence): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Prepare Request Data
        $request = Splash::ws()->pack(array(true));
        $this->assertNotEmpty($request, "Connect Request is Empty..?");
        //====================================================================//
        //   Execute Connect From Splash Server to Module
        $response = SplashServer::connect(Splash::configuration()->WsIdentifier, (string) $request);
        $data = $this->checkResponse($response);
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data["result"], "Connect Result is not True");

        //====================================================================//
        //   SAFETY CHECK
        //====================================================================//

        //====================================================================//
        //   Execute Connect with No Server Id
        $noId = SplashServer::connect("", (string) $request);
        $this->assertEmpty($noId, "Connection with No Server Id MUST be rejected => Empty Response");

        //====================================================================//
        //   Execute Connect with Wrong Server Id
        $wrongId = SplashServer::connect((string) rand((int) 1E6, (int) 1E10), (string) $request);
        $this->assertEmpty($wrongId, "Connection with Wrong Server Id MUST be rejected => Empty Response");

        //====================================================================//
        //   Turn On Output Buffering Again
        ob_start();
    }

    /**
     * Test of Server Connect with Wrong Data
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testConnectServerWrongDataAction(string $testSequence): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Prepare Ok Request Data
        $request = Splash::ws()->pack(array(true));
        //====================================================================//
        //   Change WebService Encryption Key
        Splash::configuration()->WsEncryptionKey = (string) rand((int) 1E6, (int) 1E10);
        Splash::ws()->setup();
        //====================================================================//
        //   Prepare Request Data
        $wrongRequest = Splash::ws()->pack(array(true));
        $this->assertNotSame($request, $wrongRequest);
        //====================================================================//
        //   Restore WebService Encryption Key
        Splash::reboot();

        //====================================================================//
        //   Execute Connect with Right Server Id but Wrong Encryption
        //====================================================================//
        $wrongResponse = SplashServer::connect(Splash::configuration()->WsIdentifier, (string)  $wrongRequest);
        //====================================================================//
        //   Verify Response
        $this->assertEmpty($wrongResponse, "Connection with Wrong Data Encryption MUST be rejected => Empty Response");

        //====================================================================//
        //   Re-Execute Connect From Splash Server to Module
        $this->testConnectServerAction($testSequence);
    }
}
