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
 * Core Test Suite - Test & Verifications for Splash Fields Collection Filtering
 */
class T122FieldCollectionFilteringTest extends AbstractFieldTestCase
{
    const MAX = 10;

    /**
     * Test Filtering on Fields Identifiers.
     */
    public function testFilterOnFieldsIdentifiers(): void
    {
        $fieldIds = array();
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, $fieldId = uniqid('field_'))
            );
            $fieldIds[] = $fieldId;
        }
        Assert::assertCount(self::MAX, $collection);
        Assert::assertCount(self::MAX, $collection->filterIdentifiers($fieldIds));
        //====================================================================//
        // Search for partial Field IDs
        foreach (array_chunk($fieldIds, 2) as $fieldIdsChunk) {
            Assert::assertCount(count($fieldIdsChunk), $collection->filterIdentifiers($fieldIdsChunk));
        }
        //====================================================================//
        // Search for NON Existing Field IDs
        Assert::assertCount(0, $collection->filterIdentifiers(array(uniqid('field_'))));
        Assert::assertNull($collection->find(uniqid('field_')));
        //====================================================================//
        // Search for Unique Field IDs
        foreach ($fieldIds as $fieldId) {
            Assert::assertCount(1, $collection->filterIdentifiers(array($fieldId)));
            Assert::assertNotEmpty($found = $collection->find($fieldId));
            Assert::assertEquals($fieldId, (string) $found);
            Assert::assertEquals($fieldId, $found->getIdentifier());
        }
    }

    /**
     * Test Filtering on Required Fields.
     */
    public function testFilterOnRequiredFields(): void
    {
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }
        Assert::assertCount(self::MAX, $collection);
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field as Required
        $randField->setRequired(true);
        Assert::assertTrue($randField->isRequired());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertTrue($collectionField->isRequired());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterRequired();
        Assert::assertCount(1, $filteredCollection);
        Assert::assertCount(1, $filteredCollection->filterRequired());
        Assert::assertCount(self::MAX - 1, $collection->filterRequired(false));
        //====================================================================//
        // Get Unique Field
        Assert::assertCount(1, $filteredCollection);
        Assert::assertNotEmpty($filteredCollection->unique());
    }

    /**
     * Test Filtering on Read Fields.
     */
    public function testFilterOnReadFields(): void
    {
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }
        Assert::assertCount(self::MAX, $collection);
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field as Required
        $randField->setRead(false);
        Assert::assertFalse($randField->isRead());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertFalse($collectionField->isRead());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterRead();
        Assert::assertCount(9, $filteredCollection);
        Assert::assertCount(1, $collection->filterRead(false));
        //====================================================================//
        // Get Unique Field
        Assert::assertEmpty($filteredCollection->unique());
    }

    /**
     * Test Filtering on Write Fields.
     */
    public function testFilterOnWriteFields(): void
    {
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }
        Assert::assertCount(self::MAX, $collection);
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field as Required
        $randField->setWrite(false);
        Assert::assertFalse($randField->isWrite());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertFalse($collectionField->isWrite());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterWrite();
        Assert::assertCount(9, $filteredCollection);
        Assert::assertCount(1, $collection->filterWrite(false));
        //====================================================================//
        // Get Unique Field
        Assert::assertEmpty($filteredCollection->unique());
    }

    /**
     * Test Filtering on Read & Write Fields.
     */
    public function testFilterOnReadAndWriteFields(): void
    {
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $mockField = $this->assertNewField(SplFields::VARCHAR, uniqid('field_'));
            $mockField->setRead(false);
            $mockField->setWrite(false);
            $collection->add($mockField);
        }
        Assert::assertCount(self::MAX, $collection);
        Assert::assertCount(0, $collection->filterReadAndWrite());
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field as Required
        $randField->setRead(true);
        $randField->setWrite(true);
        Assert::assertTrue($randField->isRead() && $randField->isWrite());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterReadAndWrite();
        Assert::assertCount(1, $filteredCollection);
        Assert::assertCount(1, $collection->filterReadAndWrite());
        //====================================================================//
        // Get Unique Field
        Assert::assertCount(1, $filteredCollection);
        Assert::assertNotEmpty($filteredCollection->unique());
    }

    /**
     * Test Filtering on Primary Fields.
     */
    public function testFilterOnPrimaryFields(): void
    {
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }

        Assert::assertCount(self::MAX, $collection);
        Assert::assertCount(0, $collection->filterPrimary());
        Assert::assertNull($collection->findOneByPrimary());
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field as Required
        $randField->setPrimary(true);
        Assert::assertTrue($randField->isPrimary());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertTrue($collectionField->isPrimary());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterPrimary();
        Assert::assertCount(1, $filteredCollection);
        Assert::assertCount(1, $collection->filterPrimary());
        //====================================================================//
        // Get Unique Field
        Assert::assertCount(1, $filteredCollection);
        Assert::assertNotEmpty($filteredCollection->unique());
        Assert::assertCount(1, $collection->filterPrimary());
        Assert::assertNotEmpty($collection->findOneByPrimary());
    }

    /**
     * Test Filtering on Indexed Fields.
     */
    public function testFilterOnIndexedFields(): void
    {
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }

        Assert::assertCount(self::MAX, $collection);
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field as Indexed
        $randField->setIndex(true);
        Assert::assertTrue($randField->isIndex());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertTrue($collectionField->isIndex());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterIndexed();
        Assert::assertCount(1, $filteredCollection);
        Assert::assertCount(1, $collection->filterIndexed());
        //====================================================================//
        // Get Unique Field
        Assert::assertCount(1, $filteredCollection);
        Assert::assertNotEmpty($filteredCollection->unique());
    }

    /**
     * Test Filtering on Versioned / Logged Fields.
     */
    public function testFilterOnLoggedFields(): void
    {
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }

        Assert::assertCount(self::MAX, $collection);
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field as Logged
        $randField->setLogged(true);
        Assert::assertTrue($randField->isLogged());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertTrue($collectionField->isLogged());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterLogged();
        Assert::assertCount(1, $filteredCollection);
        Assert::assertCount(1, $collection->filterLogged());
        //====================================================================//
        // Get Unique Field
        Assert::assertCount(1, $filteredCollection);
        Assert::assertNotEmpty($filteredCollection->unique());
    }

    /**
     * Test Filtering on Fields Group Name.
     */
    public function testFilterOnFieldsGroup(): void
    {
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }

        Assert::assertCount(self::MAX, $collection);
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field in Group
        $group = uniqid('group_');
        $randField->setGroup($group);
        Assert::assertEquals($group, $randField->getGroup());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertEquals($group, $collectionField->getGroup());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterGroup($group);
        Assert::assertCount(1, $filteredCollection);
        Assert::assertCount(1, $collection->filterGroup($group));
        //====================================================================//
        // Get Unique Field
        Assert::assertCount(1, $filteredCollection);
        Assert::assertNotEmpty($filteredCollection->unique());
    }

    /**
     * Test Filtering on Fields Metadata.
     */
    public function testFilterOnMetadata(): void
    {
        $itemType = uniqid('https://schema.org/');
        $itemProp = uniqid('Property');
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }
        Assert::assertCount(self::MAX, $collection);
        Assert::assertCount(0, $collection->filterMetadata($itemType, $itemProp));
        Assert::assertNull($collection->findOneByMetadata($itemType, $itemProp));
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field with Microdata
        $randField->setMicroData($itemType, $itemProp);
        Assert::assertEquals($itemType, $randField->getItemType());
        Assert::assertEquals($itemProp, $randField->getItemProp());
        Assert::assertNotEmpty($randField->getTag());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertEquals($itemType, $collectionField->getItemType());
        Assert::assertEquals($itemProp, $collectionField->getItemProp());
        Assert::assertNotEmpty($collectionField->getTag());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterMetadata($itemType, $itemProp);
        Assert::assertCount(1, $filteredCollection);
        Assert::assertCount(1, $collection->filterMetadata($itemType, $itemProp));
        //====================================================================//
        // Get Unique Field
        Assert::assertCount(1, $filteredCollection);
        Assert::assertNotEmpty($filteredCollection->unique());
        Assert::assertCount(1, $collection->filterMetadata($itemType, $itemProp));
        Assert::assertNotEmpty($collection->findOneByMetadata($itemType, $itemProp));
    }

    /**
     * Test Filtering on Fields Tag.
     */
    public function testFilterOnTag(): void
    {
        $tag = uniqid('tag_');
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }
        Assert::assertCount(self::MAX, $collection);
        Assert::assertCount(0, $collection->filterTag($tag));
        Assert::assertNull($collection->findOneByTag($tag));
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field as Tagged
        $randField->setTag($tag);
        Assert::assertEquals($tag, $randField->getTag());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertEquals($tag, $collectionField->getTag());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterTag($tag);
        Assert::assertCount(1, $filteredCollection);
        Assert::assertCount(1, $collection->filterTag($tag));
        //====================================================================//
        // Get Unique Field
        Assert::assertCount(1, $filteredCollection);
        Assert::assertNotEmpty($filteredCollection->unique());
        Assert::assertCount(1, $collection->filterTag($tag));
        Assert::assertNotEmpty($collection->findOneByTag($tag));
    }

    /**
     * Test Filtering on Listed Fields.
     */
    public function testFilterOnListed(): void
    {
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }
        Assert::assertCount(self::MAX, $collection);
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field as Listed
        $randField->setListed(true);
        Assert::assertTrue($randField->isListed());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertTrue($collectionField->isListed());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterListed();
        Assert::assertCount(1, $filteredCollection);
        Assert::assertCount(1, $collection->filterListed());
        //====================================================================//
        // Get Unique Field
        Assert::assertCount(1, $filteredCollection);
        Assert::assertNotEmpty($filteredCollection->unique());
    }

    /**
     * Test Filtering on Validated Fields.
     */
    public function testFilterOnValidated(): void
    {
        //====================================================================//
        // Create and populate collection with Valid Fields
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $validField = $this->assertNewField(SplFields::VARCHAR, uniqid('valid_field_'));
            $validField->setName("Valid Field");
            $collection->add($validField);
        }
        Assert::assertCount(self::MAX, $collection);
        Assert::assertTrue($collection->isValid());
        //====================================================================//
        // Add a Invalid Fields
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('invalid_field_'))
            );
        }
        Assert::assertCount(2 * self::MAX, $collection);
        //====================================================================//
        // Get Filtered Collection
        Assert::assertCount(self::MAX, $collection->filterValid());
        Assert::assertCount(self::MAX, $collection->filterValid(false));
        //====================================================================//
        // Verify Fields
        foreach ($collection->filterValid() as $field) {
            Assert::assertTrue($field->isValid());
        }
        foreach ($collection->filterValid(false) as $field) {
            Assert::assertFalse($field->isValid());
        }
    }

    /**
     * Test Filtering on Tested Fields.
     */
    public function testFilterOnTested(): void
    {
        //====================================================================//
        // Create and populate collection
        $collection = new FieldsCollection();
        for ($i = 0; $i < self::MAX; $i++) {
            $collection->add(
                $this->assertNewField(SplFields::VARCHAR, uniqid('field_'))
            );
        }
        Assert::assertCount(self::MAX, $collection);
        //====================================================================//
        // Pick a Random Field
        $randFieldId = (string) array_rand(array_flip(array_keys($collection->getArrayCopy())));
        $randField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $randField);
        //====================================================================//
        // Mark Field as Tested
        $randField->setNotTested(true);
        Assert::assertTrue($randField->isNotTested());
        $collectionField = $collection->get($randFieldId);
        Assert::assertInstanceOf(AbstractField::class, $collectionField);
        Assert::assertTrue($collectionField->isNotTested());
        //====================================================================//
        // Get Filtered Collection
        $filteredCollection = $collection->filterNotTested();
        Assert::assertCount(1, $filteredCollection);
        Assert::assertCount(1, $collection->filterNotTested());
        Assert::assertCount(1, $filteredCollection);
        Assert::assertNotEmpty($filteredCollection->unique());
        $filteredCollection = $collection->filterTested();
        Assert::assertCount(self::MAX - 1, $filteredCollection);
        Assert::assertCount(self::MAX - 1, $collection->filterTested());
    }
}
