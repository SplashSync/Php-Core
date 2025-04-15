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

namespace Splash\Core\Interfaces\Fields\Collection;

use Splash\Core\Fields\FilteredCollection;

/**
 * Interface for Fields Collection Filtering Methods
 */
interface FieldsFilteringInterface
{
    /**
     * Filter Fields with Given Identifiers
     *
     * @param string[] $fieldIds List of Fields Identifiers
     */
    public function filterIdentifiers(array $fieldIds): FilteredCollection;

    /**
     * Filter Fields that are Marked as Required
     */
    public function filterRequired(bool $required = true): FilteredCollection;

    /**
     * Filter Fields that are Marked as Read
     */
    public function filterRead(bool $read = true): FilteredCollection;

    /**
     * Filter Fields that are Marked for Write
     */
    public function filterWrite(bool $write = true): FilteredCollection;

    /**
     * Filter Fields that are Marked for Read & Write
     */
    public function filterReadAndWrite(): FilteredCollection;

    /**
     * Filter Fields that are Marked as Primary Field
     */
    public function filterPrimary(): FilteredCollection;

    /**
     * Filter Fields that are Marked for Indexed
     */
    public function filterIndexed(): FilteredCollection;

    /**
     * Filter Fields that are Marked for Versioning
     */
    public function filterLogged(): FilteredCollection;

    /**
     * Filter Fields that are in a Specified Group
     */
    public function filterGroup(string $group): FilteredCollection;

    /**
     * Filter Fields that have Specified Metadata
     */
    public function filterMetadata(string $itemType, string $itemProp): FilteredCollection;

    /**
     * Filter Fields that have Specified Tag
     */
    public function filterTag(string $tag): FilteredCollection;

    /**
     * Filter Fields that are Marked for Object Listing
     */
    public function filterListed(): FilteredCollection;

    /**
     * Filter Fields that are Invalid
     */
    public function filterValid(bool $valid = true): FilteredCollection;

    /**
     * Filter Fields that are Marked for Testing
     */
    public function filterTested(): FilteredCollection;

    /**
     * Filter Fields that are Not Marked for Testing
     */
    public function filterNotTested(): FilteredCollection;
}
