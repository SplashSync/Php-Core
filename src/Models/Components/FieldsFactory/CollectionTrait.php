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

namespace Splash\Core\Models\Components\FieldsFactory;

use Splash\Core\Client\Splash;
use Splash\Core\Fields\FieldsCollection;
use Splash\Core\Fields\ObjectField;
use Splash\Core\Interfaces\Fields\FieldInterface;
use Splash\Core\Models\Fields\AbstractField;

/**
 * Manage Fields Collection for Fields factory
 *
 * @phpstan-import-type FIELD from FieldInterface
 */
trait CollectionTrait
{
    /**
     * Currently Edited Object Field
     *
     * @var null|ObjectField
     */
    private ?ObjectField $current = null;

    /**
     * Object Fields Collection
     */
    private FieldsCollection $fields;

    /**
     * Get Currently Edited Field
     */
    public function current(bool $silent = false): ?ObjectField
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->current)) {
            return $silent ? null : Splash::log()->errNull("ErrFieldsNoNew");
        }

        return $this->current;
    }

    /**
     * Get Field Collection
     */
    public function getCollection(): FieldsCollection
    {
        return $this->fields ??= new FieldsCollection();
    }

    /**
     * Get All Fields
     *
     * @return array<string, FIELD>
     */
    public function toArray(): array
    {
        return $this->getCollection()->toArray();
    }

    /**
     * Get Field Collection
     */
    public function resetCollection(): void
    {
        $this->current = null;
        $this->fields = new FieldsCollection();
    }

    /**
     * Check if Field ID is Defined
     *
     * @param string $fieldId
     *
     * @return bool
     */
    public function has(string $fieldId): bool
    {
        return $this->fields->has($fieldId);
    }

    /**
     * Get Field by ID
     */
    public function get(string $fieldId): ?AbstractField
    {
        return $this->fields->get($fieldId);
    }

    /**
     * Merge an Array of Fields on Field Factory
     */
    public function merge(FieldsCollection $fields): self
    {
        //====================================================================//
        // Commit Last Created if not already done
        $this->commit();
        //====================================================================//
        // Merge Fields with Current Collection
        $this->fields->merge($fields);

        return $this;
    }

    /**
     * Merge an Array of Fields on Field Factory
     *
     * @param array<string, FIELD> $fields
     */
    public function mergeArray(array $fields): self
    {
        return $this->merge(FieldsCollection::fromArray($fields));
    }
}
