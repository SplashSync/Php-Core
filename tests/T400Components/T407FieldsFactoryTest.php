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

namespace Splash\Core\Tests\T400Components;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Splash\Core\Components\FieldsFactory;
use Splash\Core\Dictionary\Fields\SplFieldConstraints;
use Splash\Core\Dictionary\Fields\SplFieldProps;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Helpers\InlineHelper;
use Splash\Core\Models\Fields\AbstractField;
use Splash\Local\Fields\Templates\AlternateTagsTemplate;
use Splash\Local\Fields\Templates\DummyTemplate;

/**
 * Components Test Suite - Fields Factory Verifications
 */
class T407FieldsFactoryTest extends TestCase
{
    /**
     * Test the creation of a field with fieldId and fieldName
     */
    public function testBasicFieldsCreation(): void
    {
        $factory = new FieldsFactory();
        //====================================================================//
        // Create Field With Type Only
        $factory
            ->create(SplFields::VARCHAR)
            ->identifier(uniqid("id_"))
            ->name(uniqid("name_"))
        ;
        //====================================================================//
        // Create Field With ID And Name
        $factory
            ->create(SplFields::VARCHAR, uniqid("id_"))
            ->name(uniqid("name_"))
        ;
        //====================================================================//
        // Create Field With ID And Name
        $factory->create(SplFields::VARCHAR, uniqid("id_"), uniqid("name_"));
        //====================================================================//
        // Verify
        Assert::assertCount(3, $factory->build()->getCollection());
        Assert::assertCount(3, $factory->toArray());
        Assert::assertCount(3, $factory->publish() ?? array());
    }

    /**
     * Test Creating Invalid Fields is non Blocking
     */
    public function testInvalidFieldsCreation(): void
    {
        $factory = new FieldsFactory();

        //====================================================================//
        // Create valid fields
        $factory->create(SplFields::VARCHAR, "field_one", "Field One");
        $factory->create(SplFields::VARCHAR, "field_two", "Field Two");
        //====================================================================//
        // Create invalid field
        $factory->create(SplFields::VARCHAR);
        //====================================================================//
        // Create valid fields
        $factory->create(SplFields::VARCHAR, "field_three", "Field Three");
        $factory->create(SplFields::VARCHAR, "field_four", "Field Four");
        //====================================================================//
        // Verify
        Assert::assertCount(4, $factory->build()->getCollection());
        Assert::assertCount(4, $factory->toArray());
        Assert::assertCount(4, $factory->publish() ?? array());
    }

    public function testCreateFromTemplate(): void
    {
        $factory = new FieldsFactory();

        //====================================================================//
        // Create from a Dummy Template
        $factory->createFromTemplate("testFieldId", DummyTemplate::class);
        //====================================================================//
        // Verify
        Assert::assertCount(1, $factory->build()->getCollection());
        Assert::assertCount(1, $factory->toArray());
        $templateField = $factory->get("testFieldId");
        Assert::assertInstanceOf(AbstractField::class, $templateField);
        Assert::assertEquals(SplFields::TEXT, $templateField->getType());
        Assert::assertEquals("Dummy Field", $templateField->getName());
        Assert::assertCount(1, $factory->publish() ?? array());

        //====================================================================//
        // Create from a Dummy Template
        $factory->createFromTemplate("invalidFieldId", AbstractField::class);
        Assert::assertCount(0, $factory->build()->getCollection());
        Assert::assertCount(0, $factory->toArray());
    }

    /**
     * Alternate microdata and tags apply on the current field.
     */
    public function testAlternateMicroData(): void
    {
        $factory = new FieldsFactory();
        $legacyTag = AbstractField::toTag("http://schema.org/Invoice", "customer");
        $otherTag = AbstractField::toTag("http://schema.org/Order", "customer");

        //====================================================================//
        // Alternate microdata on the current field, own microdata ignored
        $factory->create(SplFields::VARCHAR)
            ->identifier("customer")
            ->name("Customer")
            ->microData("http://schema.org/Organization", "ID")
            ->alternateMicroData("http://schema.org/Invoice", "customer")
            ->alternateMicroData("http://schema.org/Organization", "ID")
        ;
        $current = $factory->current();
        Assert::assertInstanceOf(AbstractField::class, $current);
        Assert::assertSame(array($legacyTag), $current->getAlternateTags());
        //====================================================================//
        // Replace the whole list, then build
        $factory->alternateTags(array($otherTag, $legacyTag));
        $field = $factory->build()->get("customer");
        Assert::assertInstanceOf(AbstractField::class, $field);
        Assert::assertSame(array($otherTag, $legacyTag), $field->getAlternateTags());
        //====================================================================//
        // Without a current field, nothing happens
        $empty = new FieldsFactory();
        $empty->alternateMicroData("http://schema.org/Invoice", "customer")->alternateTags(array($otherTag));
        Assert::assertCount(0, $empty->build()->getCollection());
    }

    /**
     * A template declares alternate tags, applied and exported with the field.
     */
    public function testCreateFromTemplateWithAlternateTags(): void
    {
        $factory = new FieldsFactory();
        $factory->createFromTemplate("customer", AlternateTagsTemplate::class);
        $legacyTag = AbstractField::toTag(
            AlternateTagsTemplate::ALT_ITEM_TYPE,
            AlternateTagsTemplate::ALT_ITEM_PROP
        );

        $field = $factory->build()->get("customer");
        Assert::assertInstanceOf(AbstractField::class, $field);
        Assert::assertSame(
            AbstractField::toTag(AlternateTagsTemplate::ITEM_TYPE, AlternateTagsTemplate::ITEM_PROP),
            $field->getTag()
        );
        Assert::assertSame(array($legacyTag), $field->getAlternateTags());
        //====================================================================//
        // Exported with the field definition
        $published = $factory->publish() ?? array();
        Assert::assertCount(1, $published);
        $rawField = reset($published);
        Assert::assertIsArray($rawField);
        Assert::assertSame(
            array(SplFieldConstraints::TEMPLATE => AlternateTagsTemplate::getCode()),
            array_diff_key($rawField[SplFieldProps::OPTIONS], array(SplFieldConstraints::ALT_TAGS => null))
        );
        Assert::assertSame(
            InlineHelper::fromArray(array($legacyTag)),
            $rawField[SplFieldProps::OPTIONS][SplFieldConstraints::ALT_TAGS]
        );
    }
}
