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

namespace Splash\Core\Tests\T100Core\T120FieldsCollections;

use PHPUnit\Framework\Assert;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Fields\FieldsCollection;
use Splash\Core\Models\Fields\AbstractField;
use Splash\Core\Tests\T100Core\T110ObjectFields\AbstractFieldTestCase;

/**
 * Core Test Suite - Test & Verifications for Splash Fields Collection
 */
class T121FieldCollectionCoreTest extends AbstractFieldTestCase
{
    /**
     * Test with an empty array.
     */
    public function testFromArrayWithEmptyArray(): void
    {
        $collection = FieldsCollection::fromArray(array());

        Assert::assertInstanceOf(FieldsCollection::class, $collection);
        Assert::assertCount(0, $collection);
    }

    /**
     * Test with an array of ObjectField instances.
     */
    public function testFromArrayWithObjectFieldInstances(): void
    {
        $fields = array(
            $this->assertNewField(SplFields::VARCHAR, uniqid('field_')),
            $this->assertNewField(SplFields::VARCHAR, uniqid('field_')),
        );

        $collection = FieldsCollection::fromArray($fields);

        Assert::assertInstanceOf(FieldsCollection::class, $collection);
        Assert::assertCount(2, $collection);
    }

    /**
     * Test with an array of valid field arrays.
     */
    public function testFromArrayWithValidFieldArrays(): void
    {
        $fieldArray1 = $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))->toArray();
        $fieldArray2 = $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))->toArray();
        $collection = FieldsCollection::fromArray(array($fieldArray1, $fieldArray2));

        Assert::assertInstanceOf(FieldsCollection::class, $collection);
        Assert::assertCount(2, $collection);
    }

    /**
     * Test handling of mixed valid and invalid data.
     */
    public function testFromArrayWithMixedData(): void
    {
        $fieldArray = $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))->toArray();
        $validField = $this->assertNewField(SplFields::VARCHAR, uniqid('field_'));
        $invalidField = array("invalid", "data", "array", "with", "invalid", "data", "in", "it");

        $collection = FieldsCollection::fromArray(array($fieldArray, $validField, $invalidField));

        Assert::assertInstanceOf(FieldsCollection::class, $collection);
        Assert::assertCount(2, $collection);
    }

    /**
     * Test with invalid data types.
     */
    public function testFromArrayWithInvalidData(): void
    {
        $collection = FieldsCollection::fromArray(array(
            array(123),
            array('invalid'),
            array(null)
        ));

        Assert::assertInstanceOf(FieldsCollection::class, $collection);
        Assert::assertCount(0, $collection);
    }

    /**
     * Test with invalid data types.
     */
    public function testFromArrayWithSameIdentifiers(): void
    {
        $fieldId = uniqid('field_');
        $fields = array(
            $this->assertNewField(SplFields::VARCHAR, $fieldId),
            $this->assertNewField(SplFields::VARCHAR, $fieldId),
        );

        $collection = FieldsCollection::fromArray($fields);
        Assert::assertInstanceOf(FieldsCollection::class, $collection);
        Assert::assertCount(1, $collection);
    }

    /**
     * Test getting a field by its ID when field exists
     */
    public function testGetFieldExists(): void
    {
        $fieldId = uniqid('field_');
        $fieldMock = $this->assertNewField(SplFields::VARCHAR, $fieldId);

        //====================================================================//
        // Create and populate collection
        $collection = FieldsCollection::fromArray(array($fieldMock));
        Assert::assertInstanceOf(FieldsCollection::class, $collection);
        Assert::assertCount(1, $collection);

        //====================================================================//
        // Assert the field can be retrieved
        $retrievedField = $collection->get($fieldId);
        $this->assertInstanceOf(AbstractField::class, $retrievedField);
        $this->assertSame($fieldMock, $retrievedField);
    }

    /**
     * Test getting a field by its ID when field does not exist
     */
    public function testGetFieldDoesNotExist(): void
    {
        $fieldId = uniqid('field_');
        $fieldMock = $this->assertNewField(SplFields::VARCHAR, $fieldId);

        //====================================================================//
        // Create and populate collection
        $collection = FieldsCollection::fromArray(array($fieldMock));
        Assert::assertInstanceOf(FieldsCollection::class, $collection);
        Assert::assertCount(1, $collection);

        //====================================================================//
        // Assert null is returned for non-existent field
        $fieldId = "non_existent_field_id";
        $retrievedField = $collection->get($fieldId);
        $this->assertNull($retrievedField);
    }

    /**
     * Test get method with a field that is not of AbstractField instance
     */
    public function testGetFieldInvalidType(): void
    {
        //====================================================================//
        // Create collection and add invalid field data
        $fieldId = "invalid_field_id";
        $collection = new FieldsCollection();
        $collection->offsetSet($fieldId, "invalid_data");

        //====================================================================//
        // Assert null is returned for invalid field type
        $retrievedField = $collection->get($fieldId);
        $this->assertNull($retrievedField);
    }
}
