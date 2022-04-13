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
 * Admin Test Suite - SelfTest Client Verifications
 */
class A05SelfTestsTest extends AbstractBaseCase
{
    /**
     * Test Perform SelfTest from Local Class
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testSelfTestFromClass(string $testSequence): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);
        //====================================================================//
        //   Execute Action From Module
        $data = Splash::local()->selfTest();
        //====================================================================//
        //   Verify Response
        $this->verifyResponse((string) $data);
    }

    /**
     * Test Perform SelfTest from Admin Service
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @throws Exception
     *
     * @return void
     */
    public function testSelfTestFromAdmin(string $testSequence): void
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericStringAction(SPL_S_ADMIN, SPL_F_GET_SELFTEST, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * Verify Client Response.
     *
     * @param string $data
     *
     * @return void
     */
    private function verifyResponse(string $data): void
    {
        //====================================================================//
        //   Render Logs if Fails*
        if (true != $data) {
            fwrite(STDOUT, Splash::log()->getConsoleLog());
        }
        //====================================================================//
        //   Verify Response
        $this->assertIsSplashBool($data, "SelfTest");
        $this->assertNotEmpty($data, "SelfTest not Passed!! Check logs to see why!");
    }
}
