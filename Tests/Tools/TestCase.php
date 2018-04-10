<?php

/*
 * Copyright (C) 2011-2018  Splash Sync       <contact@splashsync.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 */

namespace Splash\Tests\Tools;

use PHPUnit\Framework\TestCase      as BaseTestCase;

/**
 * @abstract    Base PhpUnit Test Class for Splash Modules Tests
 *              May be overriden for Using Splash Core Test in Specific Environements
 */
if (PHP_VERSION_ID > 70000) {
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
