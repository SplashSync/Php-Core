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

use PHPUnit\Framework\Assert;
use Splash\Core\Dictionary\Fields\SplFieldProps as Props;
use Splash\Core\Dictionary\Fields\SplSyncMode;

/**
 * Core Test Suite - Test & Verifications for Splash Fields Model
 */
class T111AbstractFieldSyncModeTest extends AbstractFieldTestCase
{
    /**
     * Test Splash Field SyncMode Property Setup.
     */
    public function testSyncModeProperty(): void
    {
        //====================================================================//
        // Setup on Construct
        $mockField = $this->assertNewField();
        //====================================================================//
        // Default Value
        Assert::assertEquals(SplSyncMode::BOTH, $mockField->getSyncMode());
        Assert::assertEquals(SplSyncMode::BOTH, $mockField->toArray()[Props::SYNC_MODE]);
        //====================================================================//
        // Direct Setup
        foreach (SplSyncMode::getAll() as $mode) {
            $mockField->setSyncMode($mode);
            Assert::assertEquals($mode, $mockField->getSyncMode());
        }
        $randMode = array_rand(array_flip(SplSyncMode::getAll()));
        Assert::assertTrue(SplSyncMode::isValid($randMode));
        $mockField->setSyncMode($randMode);
        Assert::assertEquals($randMode, $mockField->getSyncMode());
        //====================================================================//
        // No Unexpected Update
        $mockField->update(array());
        Assert::assertEquals($randMode, $mockField->getSyncMode());
        $mockField->update(array(Props::SYNC_MODE => ""));
        Assert::assertEquals($randMode, $mockField->getSyncMode());
        $mockField->update(array(Props::SYNC_MODE => "Unknown Sync Mode"));
        Assert::assertEquals($randMode, $mockField->getSyncMode());

        //====================================================================//
        // Expected Update
        foreach (SplSyncMode::getAll() as $mode) {
            Assert::assertTrue(SplSyncMode::isValid($mode));
            $mockField->update(array(Props::SYNC_MODE => $mode));
            Assert::assertEquals($mode, $mockField->getSyncMode());
            Assert::assertEquals($mode, $mockField->toArray()[Props::SYNC_MODE]);
        }
    }

    /**
     * Test SyncMode Related Flags.
     *
     * @dataProvider syncModeStatusProvider
     */
    public function testSyncModeFlags(string $mode, bool $none, bool $read, bool $write, bool $both): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Direct Setup
        $mockField->setSyncMode($mode);
        Assert::assertEquals($none, $mockField->isPreferNone());
        Assert::assertEquals($read, $mockField->isPreferRead());
        Assert::assertEquals($write, $mockField->isPreferWrite());
        Assert::assertEquals($both, $mockField->isPreferBoth());

        $updateField = $this->assertNewField();
        //====================================================================//
        // Expected Update
        $updateField->update(array(Props::SYNC_MODE => $mode));
        Assert::assertEquals($none, $updateField->isPreferNone());
        Assert::assertEquals($read, $updateField->isPreferRead());
        Assert::assertEquals($write, $updateField->isPreferWrite());
        Assert::assertEquals($both, $updateField->isPreferBoth());
    }

    /**
     * Generate Flags Values for Sync Modes.
     */
    public function syncModeStatusProvider(): array
    {
        return array(
            "Prefer None" => array(SplSyncMode::NONE, true, false, false, false),
            "Prefer Read" => array(SplSyncMode::READ, false, true, false, false),
            "Prefer Write" => array(SplSyncMode::WRITE, false, false, true, false),
            "Prefer Both" => array(SplSyncMode::BOTH, false, false, false, true),
        );
    }
}
