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

namespace Splash\Tests\Tools;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Base PhpUnit Test Class for Splash Modules Tests
 * May be overridden for Using Splash Core Test in Specific Environments
 */
abstract class TestCase extends BaseTestCase
{
    use Traits\ConsoleLogTrait;
    use Traits\InitializationTrait;
}
