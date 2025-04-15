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

use Exception;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Client\Splash;
use Splash\Core\Interfaces\Local\LocalClassInterface;
use Splash\Core\Interfaces\Object\ObjectInterface;

/**
 * Core Test Suite - Local Class Verifications
 */
class T106LocalObjectsTest extends TestCase
{
    /**
     * Verify Local Objects are Correctly Mapped
     *
     * @throws Exception
     */
    public function testLocalObjectsExists(): void
    {
        $local = Splash::local();
        Assert::assertInstanceOf(LocalClassInterface::class, $local);
        //====================================================================//
        // Verify List is Not Empty
        Assert::assertNotEmpty(Splash::objects());
        Assert::assertTrue(in_array("Dummy", Splash::objects(), true));
    }

    /**
     * Verify Local Objects are Available
     *
     * @throws Exception
     */
    public function testLocalObjectsLoading(): void
    {
        $local = Splash::local();
        Assert::assertInstanceOf(LocalClassInterface::class, $local);
        //====================================================================//
        // Load Objects List
        Assert::assertNotEmpty($objectTypes = Splash::objects());
        //====================================================================//
        // Walk on Objects List
        foreach ($objectTypes as $objectType) {
            Assert::assertNotEmpty($object = Splash::object($objectType));
            Assert::assertInstanceOf(ObjectInterface::class, $object);
            Assert::assertNotEmpty($object->description());
        }
    }
}
