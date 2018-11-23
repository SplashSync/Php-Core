<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2018 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Tests\WsAdmin;

use Splash\Client\Splash;
use Splash\Tests\Tools\AbstractBaseCase;

/**
 * @abstract    Admin Test Suite - SelfTest Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A05SelfTestsTest extends AbstractBaseCase
{
    public function testFromLocalClass()
    {
        //====================================================================//
        //   Execute Action From Module
        $data = Splash::local()->selfTest();
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    public function testFromAdmin()
    {
        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_ADMIN, SPL_F_GET_SELFTEST, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }
    
    public function verifyResponse($data)
    {
        //====================================================================//
        //   Render Logs if Fails*
        if (!$data) {
            fwrite(STDOUT, Splash::log()->getConsoleLog());
        }
        //====================================================================//
        //   Verify Response
        $this->assertIsSplashBool($data, "SelfTest");
        $this->assertNotEmpty($data, "SelfTest not Passed!! Check logs to see why!");
    }
}
