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
use Splash\Core\Dictionary\Fields\SplFieldConstraints;
use Splash\Core\Dictionary\Fields\SplFieldProps as Props;
use Splash\Core\Dictionary\SplFields;
use Splash\Core\Fields\ObjectField;
use Splash\Core\Helpers\InlineHelper;
use Splash\Core\Models\Fields\AbstractField;

/**
 * Core Test Suite - Alternate Auto-Mapping Tags of Splash Fields
 */
class T111AbstractFieldAlternateTagsTest extends AbstractFieldTestCase
{
    /**
     * A field without alternate tags exposes no option.
     */
    public function testAlternateTagsDefaults(): void
    {
        $mockField = $this->assertNewField();

        Assert::assertSame(array(), $mockField->getAlternateTags());
        Assert::assertArrayNotHasKey(SplFieldConstraints::ALT_TAGS, $mockField->getOptions());
        Assert::assertArrayNotHasKey(SplFieldConstraints::ALT_TAGS, $mockField->toArray()[Props::OPTIONS]);
    }

    /**
     * Setters and getters round trip through the INLINE option.
     */
    public function testAlternateTagsRoundTrip(): void
    {
        $mockField = $this->assertNewField();
        $tagA = AbstractField::toTag(uniqid("TypeA"), uniqid("PropA"));
        $tagB = AbstractField::toTag(uniqid("TypeB"), uniqid("PropB"));
        //====================================================================//
        // Replace the list
        $mockField->setAlternateTags(array($tagA, $tagB));
        Assert::assertSame(array($tagA, $tagB), $mockField->getAlternateTags());
        Assert::assertSame(
            InlineHelper::fromArray(array($tagA, $tagB)),
            $mockField->getOptions()[SplFieldConstraints::ALT_TAGS]
        );
        Assert::assertSame(
            InlineHelper::fromArray(array($tagA, $tagB)),
            $mockField->toArray()[Props::OPTIONS][SplFieldConstraints::ALT_TAGS]
        );
        //====================================================================//
        // Add one more, existing ones are kept once
        $tagC = AbstractField::toTag(uniqid("TypeC"), uniqid("PropC"));
        $mockField->addAlternateTag($tagC);
        $mockField->addAlternateTag($tagA);
        Assert::assertSame(array($tagA, $tagB, $tagC), $mockField->getAlternateTags());
        //====================================================================//
        // Empty list removes the option
        $mockField->setAlternateTags(array());
        Assert::assertSame(array(), $mockField->getAlternateTags());
        Assert::assertArrayNotHasKey(SplFieldConstraints::ALT_TAGS, $mockField->getOptions());
    }

    /**
     * Invalid entries, duplicates and the field own tag are ignored.
     */
    public function testAlternateTagsValidation(): void
    {
        $mockField = $this->assertNewField();
        $mockField->setMicroData($itemType = uniqid("Type"), $itemProp = uniqid("Prop"));
        $ownTag = AbstractField::toTag($itemType, $itemProp);
        $valid = AbstractField::toTag(uniqid("Alt"), uniqid("Alt"));
        //====================================================================//
        // Setter filters its input
        $mockField->setAlternateTags(array($valid, $valid, $ownTag, "", "not-a-tag", strtoupper($valid), 12, null));
        Assert::assertSame(array($valid), $mockField->getAlternateTags());
        //====================================================================//
        // Own tag set after the alternates is still excluded on read
        $mockField->setMicroData(uniqid("Type"), uniqid("Prop"));
        $mockField->addAlternateTag((string) $mockField->getTag());
        Assert::assertSame(array($valid), $mockField->getAlternateTags());
        //====================================================================//
        // Raw option values are validated on read
        $mockField->addOption(SplFieldConstraints::ALT_TAGS, "{not json");
        Assert::assertSame(array(), $mockField->getAlternateTags());
        $mockField->addOption(SplFieldConstraints::ALT_TAGS, InlineHelper::fromArray(array("x", $valid, 5)));
        Assert::assertSame(array($valid), $mockField->getAlternateTags());
        $mockField->addOption(SplFieldConstraints::ALT_TAGS, true);
        Assert::assertSame(array(), $mockField->getAlternateTags());
    }

    /**
     * Alternate microdata are converted to tags like the main microdata.
     */
    public function testAlternateMicroData(): void
    {
        $mockField = $this->assertNewField();
        $itemType = uniqid("http://schema.org/");
        $itemProp = uniqid("prop");

        $mockField->addAlternateMicroData($itemType, $itemProp);
        Assert::assertSame(array(AbstractField::toTag($itemType, $itemProp)), $mockField->getAlternateTags());
        //====================================================================//
        // Partial microdata is ignored
        $mockField->addAlternateMicroData("", $itemProp);
        $mockField->addAlternateMicroData($itemType, "");
        Assert::assertCount(1, $mockField->getAlternateTags());
    }

    /**
     * Raw definitions import alternate tags through update() and fromArray().
     */
    public function testAlternateTagsImport(): void
    {
        $tag = AbstractField::toTag(uniqid("Type"), uniqid("Prop"));
        $definition = array(
            Props::OPTIONS => array(SplFieldConstraints::ALT_TAGS => InlineHelper::fromArray(array($tag))),
        );
        //====================================================================//
        // Update an existing field
        $mockField = $this->assertNewField();
        $mockField->update($definition);
        Assert::assertSame(array($tag), $mockField->getAlternateTags());
        //====================================================================//
        // Build a field from a raw definition
        $rawField = ObjectField::fromArray(array_merge(
            $this->assertNewField(SplFields::VARCHAR, "imported")->toArray(),
            $definition
        ));
        Assert::assertSame(array($tag), $rawField->getAlternateTags());
    }
}
