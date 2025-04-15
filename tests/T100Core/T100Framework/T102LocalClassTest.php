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

namespace Splash\Core\Tests\T100Core\T100Framework;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Client\Splash;
use Splash\Core\Interfaces\Local\LocalClassInterface;

/**
 * Core Test Suite - Local Class Verifications
 */
class T102LocalClassTest extends TestCase
{
    /**
     * Verify Local Class Exists & Correctly Mapped
     */
    public function testLocalClassExists(): void
    {
        try {
            $local = Splash::local();
        } catch (\Exception $e) {
            $local = null;
            Splash::log()->report($e);
        }
        Assert::assertNotEmpty(
            $local,
            "Splash Local Class Not found. Check you local class"
            ." is defined and auto-loaded from Namespace Splash\\Local\\Local."
            ." Or loaded on System init by Splash::setLocalClass function."
        );
        Assert::assertInstanceOf(LocalClassInterface::class, $local);
    }

    /**
     * Verify Local Path Structure is Valid
     */
    public function testLocalPaths(): void
    {
        //====================================================================//
        //   Verify Local Path Exists
        Assert::assertTrue(
            Splash::validate()->isValidLocalPath(),
            "Splash Local Class MUST define so that Splash can "
                ."detect & use it's folder as root path for local Module files."
        );
        Assert::assertDirectoryExists(
            $localPath = (string) Splash::getLocalPath(),
            "Splash Local folder was not found."
        );
        //====================================================================//
        //   Verify Local Mandatory Paths Exists
        $objectsPath = $localPath."/Objects";
        Assert::assertDirectoryExists(
            $objectsPath,
            "Splash Local Objects folder MUST be define in ".$objectsPath."."
        );
        $widgetsPath = $localPath."/Widgets";
        Assert::assertDirectoryExists(
            $widgetsPath,
            "Splash Local Widgets folder MUST be define in ".$widgetsPath."."
        );
        $translationsPath = $localPath."/Translations";
        Assert::assertDirectoryExists(
            $translationsPath,
            "Splash Local Translations folder MUST be define in ".$translationsPath."."
        );
    }
}
