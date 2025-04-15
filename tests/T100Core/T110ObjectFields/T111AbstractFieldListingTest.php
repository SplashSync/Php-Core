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
class T111AbstractFieldListingTest extends AbstractFieldTestCase
{
    /**
     * Test Splash Field Listed Property Setup.
     */
    public function testListedProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Validate Basic Boolean Property
        self::assertBooleanPropertyWorks(
            $mockField,
            Props::IN_LIST,
            array($mockField, "isListed"),
            array($mockField, "setListed")
        );
    }

    /**
     * Test Splash Field List Hidden Property Setup.
     */
    public function testListHiddenProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Validate Basic Boolean Property
        self::assertBooleanPropertyWorks(
            $mockField,
            Props::HIDDEN_IN_LIST,
            array($mockField, "isListHidden"),
            array($mockField, "setListHidden")
        );
    }
}
