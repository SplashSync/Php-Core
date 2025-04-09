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

namespace Splash\Core\Tests\T100Core;

use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Client\Splash;
use Splash\Core\Interfaces\Local\LocalClassInterface;
use Splash\Core\Models\Widgets\WidgetInterface;

/**
 * Core Test Suite - Verify Widgets Availability & Methods
 */
class T107LocalWidgetsTest extends TestCase
{
    /**
     * Verify Local Widgets are Correctly Mapped
     *
     * @throws Exception
     */
    public function testLocalWidgetsExists(): void
    {
        $local = Splash::local();
        Assert::assertInstanceOf(LocalClassInterface::class, $local);
        //====================================================================//
        // Verify List is Not Empty
        Assert::assertNotEmpty(Splash::widgets());
        Assert::assertTrue(in_array("SelfTest", Splash::widgets(), true));
    }

    /**
     * Verify Local Widgets are Available
     *
     * @throws Exception
     */
    public function testLocalWidgetsLoading(): void
    {
        $local = Splash::local();
        Assert::assertInstanceOf(LocalClassInterface::class, $local);
        //====================================================================//
        // Load Widgets List
        Assert::assertNotEmpty($widgetTypes = Splash::widgets());
        //====================================================================//
        // Walk on Widgets List
        foreach ($widgetTypes as $widgetType) {
            Assert::assertNotEmpty($widget = Splash::widget($widgetType));
            Assert::assertInstanceOf(WidgetInterface::class, $widget);
            Assert::assertNotEmpty($widget->get());
        }
    }
}
