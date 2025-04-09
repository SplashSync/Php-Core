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

namespace Splash\Core\Tests\T400Components;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Client\Splash;

/**
 * Components Test Suite - Translator Verifications
 */
class T406TranslatorTest extends TestCase
{
    /**
     * Test of Basic String Translations
     */
    public function testBasicTranslations(): void
    {
        Assert::assertEquals(
            "DebEnableDebug",
            Splash::translator()->translate("DebEnableDebug"),
        );
        Assert::assertEquals(
            "DebEnableDebug",
            Splash::trans("DebEnableDebug"),
        );
        //====================================================================//
        // Load Language file
        Splash::translator()->load("main");
        Assert::assertEquals(
            "Enable Debugging Mode",
            Splash::translator()->translate("DebEnableDebug"),
        );
        Assert::assertEquals(
            "Enable Debugging Mode",
            Splash::trans("DebEnableDebug"),
        );
    }

    /**
     * Test of Advanced String Translations
     */
    public function testAdvancedTranslations(): void
    {
        //====================================================================//
        // Load Language file
        Splash::translator()->load("main");
        Assert::assertEquals(
            "Stack Trace class->method",
            Splash::translator()->translate("DebTraceMsg", "class", "method"),
        );
        Assert::assertEquals(
            "Stack Trace class->method",
            Splash::trans("DebTraceMsg", "class", "method"),
        );
    }

    /**
     * Test of Local Module String Translations
     */
    public function testLocalTranslations(): void
    {
        Assert::assertEquals(
            "LocalModule",
            Splash::trans("LocalModule"),
        );
        //====================================================================//
        // Load Local Language file
        Splash::translator()->load("local@local");

        Assert::assertEquals(
            "Splash PhpCore",
            Splash::trans("LocalModule"),
        );
    }
}
