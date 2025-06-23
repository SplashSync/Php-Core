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

namespace Splash\Core\Models\Fields\Collections;

use Splash\Core\Fields\FilteredCollection;
use Splash\Core\Fields\ObjectField;
use Splash\Core\Models\Fields\AbstractField;

/**
 * A set of Fields Collection Filtering Methods
 */
trait FilteringTrait
{
    /**
     * @inheritDoc
     */
    public function filterIdentifiers(array $fieldIds): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => in_array((string) $field, $fieldIds, true)
        );
    }

    /**
     * @inheritDoc
     */
    public function filterRequired(bool $required = true): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => $required ? $field->isRequired() : !$field->isRequired()
        );
    }

    /**
     * @inheritDoc
     */
    public function filterRead(bool $read = true): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => $read ? $field->isRead() : !$field->isRead()
        );
    }

    /**
     * @inheritDoc
     */
    public function filterWrite(bool $write = true): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => $write ? $field->isWrite() : !$field->isWrite()
        );
    }

    /**
     * @inheritDoc
     */
    public function filterReadAndWrite(): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => $field->isRead() && $field->isWrite()
        );
    }

    /**
     * @inheritDoc
     */
    public function filterPrimary(): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => $field->isPrimary()
        );
    }

    /**
     * @inheritDoc
     */
    public function filterIndexed(): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => $field->isIndex()
        );
    }

    /**
     * @inheritDoc
     */
    public function filterLogged(): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => $field->isLogged()
        );
    }

    /**
     * @inheritDoc
     */
    public function filterGroup(string $group): FilteredCollection
    {
        //==============================================================================
        // Format Group Name
        $group = ucwords(html_entity_decode($group));

        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => ($group == $this->getGroupName($field))
        );
    }

    /**
     * @inheritDoc
     */
    public function filterType(string $type): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => ($type == $field->getType())
        );
    }

    /**
     * @inheritDoc
     */
    public function filterBaseType(string $baseType): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => ($baseType == ObjectField::baseType($field->getType()))
        );
    }

    /**
     * @inheritDoc
     */
    public function filterMetadata(string $itemType, string $itemProp): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => ($itemType == $field->getItemType() && $itemProp == $field->getItemProp())
        );
    }

    /**
     * @inheritDoc
     */
    public function filterTag(string $tag): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => ($tag == $field->getTag())
        );
    }

    /**
     * @inheritDoc
     */
    public function filterListed(): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => $field->isListed()
        );
    }

    /**
     * @inheritDoc
     */
    public function filterValid(bool $valid = true): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => $valid ? $field->isValid() : !$field->isValid()
        );
    }

    /**
     * @inheritDoc
     */
    public function filterTested(): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => !$field->isNotTested()
        );
    }

    /**
     * @inheritDoc
     */
    public function filterNotTested(): FilteredCollection
    {
        return new FilteredCollection(
            $this,
            fn (AbstractField $field) => $field->isNotTested()
        );
    }
}
