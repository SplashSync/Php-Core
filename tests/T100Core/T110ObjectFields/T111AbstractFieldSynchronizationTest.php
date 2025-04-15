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

namespace Splash\Core\Tests\T100Core\T110ObjectFields;

use Splash\Core\Dictionary\Fields\SplFieldProps as Props;

/**
 * Core Test Suite - Test & Verifications for Splash Fields Model
 */
class T111AbstractFieldSynchronizationTest extends AbstractFieldTestCase
{
    /**
     * Test Splash Field Required Property Setup.
     */
    public function testRequiredProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Validate Basic Boolean Property
        self::assertBooleanPropertyWorks(
            $mockField,
            Props::REQUIRED,
            array($mockField, "isRequired"),
            array($mockField, "setRequired")
        );
    }

    /**
     * Test Splash Field Read Property Setup.
     */
    public function testReadProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Validate Basic Boolean Property
        self::assertBooleanPropertyWorks(
            $mockField,
            Props::READ,
            array($mockField, "isRead"),
            array($mockField, "setRead"),
            true
        );
    }

    /**
     * Test Splash Field Write Property Setup.
     */
    public function testWriteProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Validate Basic Boolean Property
        self::assertBooleanPropertyWorks(
            $mockField,
            Props::WRITE,
            array($mockField, "isWrite"),
            array($mockField, "setWrite"),
            true
        );
    }

    /**
     * Test Splash Field Index Property Setup.
     */
    public function testIndexProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Validate Basic Boolean Property
        self::assertBooleanPropertyWorks(
            $mockField,
            Props::INDEX,
            array($mockField, "isIndex"),
            array($mockField, "setIndex")
        );
    }

    /**
     * Test Splash Field Primary Property Setup.
     */
    public function testPrimaryProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Validate Basic Boolean Property
        self::assertBooleanPropertyWorks(
            $mockField,
            Props::PRIMARY,
            array($mockField, "isPrimary"),
            array($mockField, "setPrimary")
        );
    }

    /**
     * Test Splash Field Logged Property Setup.
     */
    public function testLoggedProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Validate Basic Boolean Property
        self::assertBooleanPropertyWorks(
            $mockField,
            Props::LOG,
            array($mockField, "isLogged"),
            array($mockField, "setLogged")
        );
    }
}
