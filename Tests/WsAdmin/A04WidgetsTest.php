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

namespace Splash\Tests\WsAdmin;

use ArrayObject;
use Splash\Client\Splash;
use Splash\Tests\Tools\AbstractBaseCase;

/**
 * Admin Test Suite - Get Objects List Client Verifications
 *
 * @author SplashSync <contact@splashsync.com>
 */
class A04WidgetsTest extends AbstractBaseCase
{
    /**
     * Test Loading Widgets List from Local Class
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @return void
     */
    public function testWidgetsFromClass($testSequence)
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Execute Action From Module
        $data = Splash::widgets();
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
     * Test Loading Widgets List from Admin Service
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @return void
     */
    public function testWidgetsActionFromAdmin($testSequence)
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_ADMIN, SPL_F_GET_WIDGETS, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * Test Loading Widgets List from Widgets Service
     *
     * @dataProvider sequencesProvider
     *
     * @param string $testSequence
     *
     * @return void
     */
    public function testWidgetsActionFromWidgets($testSequence)
    {
        //====================================================================//
        //   Configure Env. for Test Sequence
        $this->loadLocalTestSequence($testSequence);

        //====================================================================//
        //   Execute Action From Splash Server to Module
        $data = $this->genericAction(SPL_S_WIDGETS, SPL_F_WIDGET_LIST, __METHOD__);
        //====================================================================//
        //   Verify Response
        $this->verifyResponse($data);
    }

    /**
     * Verify Client Response.
     *
     * @param ArrayObject|bool|string $data
     *
     * @return void
     */
    private function verifyResponse($data)
    {
        //====================================================================//
        //   Verify Response
        $this->assertNotEmpty($data, "Widgets List is Empty");
        $this->assertInstanceOf("ArrayObject", $data, "Widgets List is Not an ArrayObject");
        //====================================================================//
        // CHECK ITEMS
        foreach ($data as $widgetType) {
            $this->assertNotEmpty($widgetType, "Widgets Type is Empty");
            $this->assertIsString(
                $widgetType,
                "Widgets Type is Not an String. (Given".print_r($widgetType, true).")"
            );
        }
    }
}
