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
use PHPUnit\Framework\TestCase;
use Splash\Core\Dictionary\Fields\SplSyncMode;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Models\Fields\AbstractField;

/**
 * Core Test Suite - Test & Verifications for Splash Fields Model
 */
class T116AbstractFieldConversionTest extends TestCase
{
    /**
     * Test the toArray method returns expected field data when all properties are populated.
     */
    public function testToArrayWithAllPropertiesPopulated(): void
    {
        $fieldId = uniqid("test_field_");
        $mockField = $this->getMockForAbstractClass(
            AbstractField::class,
            array(SplFields::VARCHAR, $fieldId)
        );
        $mockField->setName("Test Field");
        $mockField->setDesc("This is a test field.");
        $mockField->setGroup("Test Group");
        $mockField->setRequired(true);
        $mockField->setRead(true);
        $mockField->setWrite(true);
        $mockField->setIndex(true);
        $mockField->setLogged(true);
        $mockField->setPrimary(true);

        $expectedArray = array(
            "type" => SplFields::VARCHAR,
            "id" => $fieldId,
            "name" => "Test Field",
            "desc" => "This is a test field.",
            "group" => "Test Group",
            "required" => true,
            "read" => true,
            "write" => true,
            "index" => true,
            "inlist" => false,
            "hlist" => false,
            "log" => true,
            "primary" => true,
            "syncmode" => SplSyncMode::BOTH,
            "itemtype" => null,
            "itemprop" => null,
            "tag" => null,
            "choices" => array(),
            "asso" => array(),
            "options" => array(),
            "notest" => false,
        );

        Assert::assertEquals($expectedArray, $mockField->toArray());
    }

    /**
     * Test the toArray method with only mandatory properties set.
     */
    public function testToArrayWithMandatoryPropertiesOnly(): void
    {
        $fieldId = uniqid("test_field_");
        $mockField = $this->getMockForAbstractClass(
            AbstractField::class,
            array(SplFields::VARCHAR, $fieldId)
        );

        $expectedArray = array(
            "type" => SplFields::VARCHAR,
            "id" => $fieldId,
            "name" => $fieldId,
            "desc" => null,
            "group" => null,
            "required" => false,
            "read" => true,
            "write" => true,
            "index" => false,
            "inlist" => false,
            "hlist" => false,
            "log" => false,
            "primary" => false,
            "syncmode" => SplSyncMode::BOTH,
            "itemtype" => null,
            "itemprop" => null,
            "tag" => null,
            "choices" => array(),
            "asso" => array(),
            "options" => array(),
            "notest" => false,
        );

        Assert::assertEquals($expectedArray, $mockField->toArray());
    }
}
