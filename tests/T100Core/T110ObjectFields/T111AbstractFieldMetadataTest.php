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
use Splash\Core\Models\Fields\AbstractField;

/**
 * Core Test Suite - Test & Verifications for Splash Fields Model
 */
class T111AbstractFieldMetadataTest extends AbstractFieldTestCase
{
    /**
     * Test Splash Field Metadata Properties Default Values.
     */
    public function testMetadataPropertiesDefaults(): void
    {
        //====================================================================//
        // Setup on Construct
        $mockField = $this->assertNewField();
        //====================================================================//
        // Default Value
        Assert::assertNull($mockField->getItemProp());
        Assert::assertNull($mockField->toArray()[Props::MICRODATA_URL]);
        Assert::assertNull($mockField->getItemType());
        Assert::assertNull($mockField->toArray()[Props::MICRODATA_PROP]);
        Assert::assertNull($mockField->getTag());
        Assert::assertNull($mockField->toArray()[Props::TAG]);
    }

    /**
     * Test Splash Field Metadata Properties with Invalid Values.
     *
     * @dataProvider invalidValuesProvider
     */
    public function testMetadataWithInvalidValues(string $itemType, string $itemProp): void
    {
        //====================================================================//
        // Setup on Construct
        $mockField = $this->assertNewField();
        //====================================================================//
        // Setup Value
        $mockField->setMicroData($itemType, $itemProp);
        //====================================================================//
        // Default Value
        Assert::assertNull($mockField->getItemProp());
        Assert::assertNull($mockField->toArray()[Props::MICRODATA_URL]);
        Assert::assertNull($mockField->getItemType());
        Assert::assertNull($mockField->toArray()[Props::MICRODATA_PROP]);
        Assert::assertNull($mockField->getTag());
        Assert::assertNull($mockField->toArray()[Props::TAG]);
    }

    /**
     * Test Splash Field Metadata Properties with Partial Values.
     *
     * @dataProvider invalidValuesProvider
     */
    public function testMetadataWithPartialValues(string $itemType, string $itemProp): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Setup Value
        $mockField->setMicroData($itemType, $itemProp);
        //====================================================================//
        // Default Value
        Assert::assertNull($mockField->getItemProp());
        Assert::assertNull($mockField->getItemType());
        Assert::assertNull($mockField->getTag());
        //====================================================================//
        // Setup Value with Non Empty Values
        $mockField->setMicroData(uniqid("Type"), uniqid("Prop"));
        $mockField->setMicroData($itemType, $itemProp);
        //====================================================================//
        // Verify
        if (!empty($itemType)) {
            Assert::assertEquals($itemType, $mockField->getItemType());
        }
        if (!empty($itemProp)) {
            Assert::assertEquals($itemProp, $mockField->getItemProp());
        }
    }

    /**
     * Test Splash Field Metadata Properties with Valid Values.
     */
    public function testMetadataWithValidValues(): void
    {
        $mockField = $this->assertNewField();
        //====================================================================//
        // Setup Value
        $mockField->setMicroData(
            $itemType = uniqid("Type"),
            $itemProp = uniqid("Prop")
        );
        //====================================================================//
        // Verify
        Assert::assertEquals($itemType, $mockField->getItemType());
        Assert::assertEquals($itemType, $mockField->toArray()[Props::MICRODATA_URL]);
        Assert::assertEquals($itemProp, $mockField->getItemProp());
        Assert::assertEquals($itemProp, $mockField->toArray()[Props::MICRODATA_PROP]);
        Assert::assertNotEmpty($mockField->getTag());
        Assert::assertNotEmpty($mockField->toArray()[Props::TAG]);
        $expectedTag = AbstractField::toTag($itemType, $itemProp);
        Assert::assertEquals($expectedTag, $mockField->getTag());
        Assert::assertEquals($expectedTag, $mockField->toArray()[Props::TAG]);
    }

    /**
     * Generate a Set of Invalid Metadata Values.
     */
    public function invalidValuesProvider(): array
    {
        return array(
            "Empty" => array("", ""),
            "No Prop" => array("Name", ""),
            "No Type" => array("", "Name"),
        );
    }
}
