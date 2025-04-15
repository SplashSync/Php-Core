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

namespace Splash\Core\Tests\T100Core\T130Configurators;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Configurator\StaticConfigurator;
use Splash\Core\Dictionary\Fields\SplFieldProps as Props;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Fields\FieldsCollection;
use Splash\Core\Models\Fields\AbstractField;

/**
 * Class T133FieldsConfiguratorTest
 *
 * This test class is responsible for testing the functionality of the StaticConfigurator
 * and its ability to override a FieldsCollection based on specific configurations.
 *
 * It contains multiple test methods to validate various scenarios, such as:
 * - Handling an empty configuration without altering the fields.
 * - Applying valid override configurations to modify the fields as expected.
 * - Managing scenarios with invalid overrides gracefully.
 * - Excluding specific fields from a collection based on the configuration.
 */
class T133FieldsConfiguratorTest extends TestCase
{
    const OBJECT_TYPE = 'TestObject';

    private StaticConfigurator $configurator;

    protected function setUp(): void
    {
        $this->configurator = new StaticConfigurator();
    }

    /**
     * Tests the behavior of overriding a fields collection using an empty configuration.
     *
     * This method validates the following scenarios:
     * - A fields collection is generated with predefined types and properties.
     * - An empty configuration is applied to the fields collection through the configurator.
     * - Ensures that the collection remains unchanged when no overrides are specified.
     * - Verifies that the resultant collection is identical to the initial collection.
     */
    public function testOverrideFieldsCollectionWithEmptyConfig(): void
    {
        //====================================================================//
        // Generate Field Collection
        $fields = FieldsCollection::fromArray(array(
            'field1' => array(Props::ID => "field1", Props::TYPE => SplFields::VARCHAR),
            'field2' => array(Props::ID => "field2", Props::TYPE => SplFields::BOOL)
        ));
        //====================================================================//
        // Apply Empty Configurator
        $result = $this->configurator->overrideFieldsCollection(self::OBJECT_TYPE, $fields);
        Assert::assertInstanceOf(FieldsCollection::class, $result);

        //====================================================================//
        // Verify
        Assert::assertEquals($fields->toArray(), $result->toArray());
    }

    /**
     * Tests the behavior of overriding a fields collection with specific configuration overrides.
     *
     * This method validates the following scenarios:
     * - A fields collection is generated and certain type properties are assigned.
     * - A configurator is applied to override or remove specific fields within the collection.
     * - Ensures overridden fields are updated with the specified type and properties.
     * - Verifies that excluded fields are properly removed from the collection.
     * - Confirms that fields not explicitly defined in the configuration are left unchanged.
     * - Checks that the final collection reflects the expected number of fields.
     */
    public function testOverrideFieldsCollectionWithOverrides(): void
    {
        //====================================================================//
        // Generate Field Collection
        $fields = FieldsCollection::fromArray(array(
            array(Props::ID => "field1", Props::TYPE => SplFields::VARCHAR),
            array(Props::ID => "field2", Props::TYPE => SplFields::BOOL),
        ));
        Assert::assertEquals(SplFields::VARCHAR, $fields->get("field1")?->getType());
        Assert::assertEquals(SplFields::BOOL, $fields->get("field2")?->getType());
        Assert::assertNull($fields->get("field3"));

        //====================================================================//
        // Apply Configurator
        $this->configurator->setObjectConfiguration(self::OBJECT_TYPE, array(
            'fields' => array(
                'field1' => array(Props::TYPE => SplFields::INT, 'excluded' => false),
                'field2' => array('excluded' => true),
                'field3' => array(Props::TYPE => SplFields::DATE)
            )
        ));
        $overrideFields = $this->configurator->overrideFieldsCollection(self::OBJECT_TYPE, $fields);
        Assert::assertInstanceOf(FieldsCollection::class, $overrideFields);

        //====================================================================//
        // Verify - Field 1 Was Updated
        Assert::assertTrue($overrideFields->has('field1'));
        Assert::assertInstanceOf(AbstractField::class, $field1 = $overrideFields->get('field1'));
        Assert::assertEquals(SplFields::INT, $field1->getType());
        //====================================================================//
        // Verify - Field 2 Was Removed
        Assert::assertFalse($overrideFields->has('field2'));
        //====================================================================//
        // Verify - Field 2 Was Not Created
        Assert::assertFalse($overrideFields->has('field3'));
        //====================================================================//
        // Verify - Collection Now has only One Field
        Assert::assertCount(1, $overrideFields);
    }

    /**
     * Tests the behavior of overriding a fields collection when provided with invalid configuration overrides.
     *
     * This method validates the following scenarios:
     * - A fields collection is generated with initial type properties for a single field.
     * - An invalid configuration is applied to the configurator, simulating incorrect override input.
     * - Ensures the override operation does not modify the original fields collection.
     * - Confirms the output matches the input fields collection, maintaining its structure and properties.
     */
    public function testOverrideFieldsCollectionWithInvalidOverrides(): void
    {
        //====================================================================//
        // Generate Field Collection
        $fields = FieldsCollection::fromArray(array(
            array(Props::ID => "field1", Props::TYPE => SplFields::VARCHAR),
        ));
        //====================================================================//
        // Apply Configurator
        $this->configurator->setObjectConfiguration(self::OBJECT_TYPE, array(
            'fields' => 'invalid_overrides'
        ));
        $result = $this->configurator->overrideFieldsCollection(self::OBJECT_TYPE, $fields);
        Assert::assertInstanceOf(FieldsCollection::class, $result);
        //====================================================================//
        // Verify
        Assert::assertEquals($fields->toArray(), $result->toArray());
    }

    /**
     * Tests the exclusion of a specific field from a field collection using the configurator's object configuration.
     *
     * This method generates a field collection from a predefined array of fields. It then applies a configurator
     * setting to exclude a specific field ('field1') from the collection. Once the configurator modifies the collection,
     * the test performs assertions to ensure the field marked as excluded is no longer present in the resulting collection,
     * while the other fields remain unaffected.
     */
    public function testOverrideFieldsCollectionWithFieldExclusion(): void
    {
        //====================================================================//
        // Generate Field Collection
        $fields = FieldsCollection::fromArray(array(
            array(Props::ID => "field1", Props::TYPE => SplFields::VARCHAR),
            array(Props::ID => "field2", Props::TYPE => SplFields::BOOL),
        ));
        //====================================================================//
        // Apply Configurator
        $this->configurator->setObjectConfiguration(self::OBJECT_TYPE, array(
            'fields' => array(
                'field1' => array('excluded' => true)
            )
        ));
        $result = $this->configurator->overrideFieldsCollection(self::OBJECT_TYPE, $fields);
        Assert::assertInstanceOf(FieldsCollection::class, $result);
        //====================================================================//
        // Verify
        Assert::assertFalse($result->has('field1'));
        Assert::assertTrue($result->has('field2'));
    }
}
