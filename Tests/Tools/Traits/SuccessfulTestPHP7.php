<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Tests\Tools\Traits;

use Splash\Client\Splash;

/**
 * Description of SuccessfulTestPHP7
 *
 * @author nanard33
 */
trait SuccessfulTestPHP7
{
    public function onNotSuccessfulTest(\Throwable $exception)
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
        // OutPut Phpunit Exeption
        throw $exception;
    }
}
