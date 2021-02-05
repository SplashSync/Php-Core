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

namespace Splash\Tests\Tools;

use PHPUnit\Framework\TestCase      as BaseTestCase;
use Splash\Client\Splash;
use Throwable;

if (!defined("SPLASH_SERVER_MODE")) {
    define("SPLASH_SERVER_MODE", true);
}

/**
 * Base PhpUnit Test Class for Splash Modules Tests
 * May be overriden for Using Splash Core Test in Specific Environements
 */

abstract class TestCase extends BaseTestCase
{
    /**
     * @param Throwable $exception
     *
     * @throws Throwable
     *
     * @return void
     */
    public function onNotSuccessfulTest(Throwable $exception): void
    {
        //====================================================================//
        // Do not display log on Skipped Tests
        if (is_a($exception, "PHPUnit\\Framework\\SkippedTestError")) {
            throw $exception;
        }
        //====================================================================//
        // Remove Debug From Splash Logs
        Splash::log()->deb = array();
        //====================================================================//
        // OutPut Splash Logs
        fwrite(STDOUT, Splash::log()->getConsoleLog());
        //====================================================================//
        // OutPut Phpunit Exception
        throw $exception;
    }
}
