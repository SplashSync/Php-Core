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

namespace Splash\Tests\Tools;

use PHPUnit\Framework\TestCase      as BaseTestCase;

if (!defined("SPLASH_SERVER_MODE")) {
    define("SPLASH_SERVER_MODE", true);
}

/**
 * Base PhpUnit Test Class for Splash Modules Tests
 * May be overriden for Using Splash Core Test in Specific Environements
 */
if (PHP_VERSION_ID > 70100) {
    abstract class TestCase extends BaseTestCase
    {
        use \Splash\Tests\Tools\Traits\SuccessfulTestPHP71;
    }
} elseif (PHP_VERSION_ID > 70000) {
    abstract class TestCase extends BaseTestCase
    {
        use \Splash\Tests\Tools\Traits\SuccessfulTestPHP7;
    }
} else {
    abstract class TestCase extends BaseTestCase
    {
        use \Splash\Tests\Tools\Traits\SuccessfulTestPHP5;
    }
}
