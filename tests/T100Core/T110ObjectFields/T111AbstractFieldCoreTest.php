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
use Splash\Core\Dictionary\SplFields;

/**
 * Core Test Suite - Test & Verifications for Splash Fields Model
 */
class T111AbstractFieldCoreTest extends AbstractFieldTestCase
{
    /**
     * Test Splash Field ID Property Setup.
     */
    public function testIdentifierProperty(): void
    {
        $constructId = uniqid("test_field_");
        //====================================================================//
        // Setup on Construct
        $mockField = $this->assertNewField(SplFields::VARCHAR, $constructId);
        Assert::assertEquals($constructId, $mockField->getIdentifier());
        //====================================================================//
        // To String Conversion
        Assert::assertEquals($constructId, (string) $mockField);

        $fieldId = uniqid("test_field_");
        //====================================================================//
        // Direct Setup
        $mockField->setIdentifier($fieldId);
        Assert::assertEquals($fieldId, $mockField->getIdentifier());
        //====================================================================//
        // No Unexpected Update
        $mockField->update(array());
        Assert::assertEquals($fieldId, $mockField->getIdentifier());
        $mockField->update(array(Props::ID => ""));
        Assert::assertEquals($fieldId, $mockField->getIdentifier());
        $mockField->update(array(Props::NAME => "New Identifier"));
        Assert::assertEquals($fieldId, $mockField->getIdentifier());

        //====================================================================//
        // To String Conversion
        Assert::assertEquals($fieldId, (string) $mockField);
        //====================================================================//
        // To Array Conversion
        Assert::assertEquals(
            $mockField->toArray()[Props::ID],
            $mockField->getIdentifier()
        );
    }

    /**
     * Test Splash Field Type Property Setup.
     */
    public function testTypeProperty(): void
    {
        $randType = array_rand(array_flip(SplFields::getAll()));
        Assert::assertTrue(SplFields::isValid($randType));
        //====================================================================//
        // Setup on Construct
        $mockField = $this->assertNewField($randType);
        Assert::assertEquals($randType, $mockField->getType());
        Assert::assertEquals($mockField->toArray()[Props::TYPE], $randType);
        //====================================================================//
        // No Unexpected Update
        $mockField->update(array());
        Assert::assertEquals($randType, $mockField->getType());
        $mockField->update(array(Props::TYPE => ""));
        Assert::assertEquals($randType, $mockField->getType());
        //====================================================================//
        // Expected Update
        $newType = array_rand(array_flip(SplFields::getAll()));
        Assert::assertTrue(SplFields::isValid($newType));
        $mockField->update(array(Props::TYPE => $newType));
        Assert::assertEquals($newType, $mockField->getType());
        //====================================================================//
        // To Array Conversion
        Assert::assertEquals($mockField->toArray()[Props::TYPE], $newType);
    }

    /**
     * Test Splash Field Name Property Setup.
     */
    public function testNameProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Direct Setup
        $mockField->setName($name = uniqid("Field Name "));
        Assert::assertEquals($name, $mockField->getName());
        Assert::assertEquals($name, $mockField->getDesc());
        //====================================================================//
        // To Array Conversion
        Assert::assertEquals($mockField->toArray()[Props::NAME], $name);
        //====================================================================//
        // Validate Basic String Property
        self::assertStringPropertyWorks(
            $mockField = $this->assertNewField(),
            Props::NAME,
            array($mockField, "getName"),
            array($mockField, "setName")
        );
    }

    /**
     * Test Splash Field Description Property Setup.
     */
    public function testDescriptionProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Validate Basic String Property
        self::assertStringPropertyWorks(
            $mockField,
            Props::DESC,
            array($mockField, "getDesc"),
            array($mockField, "setDesc")
        );
    }

    /**
     * Test Splash Field Group Property Setup.
     */
    public function testGroupProperty(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Validate Basic String Property
        self::assertStringPropertyWorks(
            $mockField,
            Props::GROUP,
            array($mockField, "getGroup"),
            array($mockField, "setGroup"),
        );
    }
}
