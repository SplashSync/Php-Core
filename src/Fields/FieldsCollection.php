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

namespace Splash\Core\Fields;

use ArrayIterator;
use Splash\Core\Interfaces\Fields\FieldsCollectionInterface;
use Splash\Core\Models\Fields\AbstractField;
use Splash\Core\Models\Fields\Collections\FilteringTrait;
use Splash\Core\Models\Fields\Collections\FinderTrait;
use Splash\Core\Models\Fields\Collections\GroupsTrait;

/**
 * Automated Storage Class for Objects Fields Collection
 */
class FieldsCollection extends ArrayIterator implements FieldsCollectionInterface
{
    use FilteringTrait;
    use FinderTrait;
    use GroupsTrait;

    /**
     * Collection must be filled manually
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create a Collection from an Array of Fields
     *
     * @param AbstractField[]|array[] $fields
     *
     * @return FieldsCollection
     */
    public static function fromArray(array $fields): FieldsCollection
    {
        $collection = new FieldsCollection();
        foreach ($fields as $item) {
            $field = null;

            try {
                //==============================================================================
                // Transform Field Definition to Object
                if (is_array($item) && !empty($item)) {
                    $field = ObjectField::fromArray($item);
                } elseif ($item instanceof AbstractField) {
                    $field = $item;
                }
            } catch (\Exception $e) {
                continue;
            }
            //==============================================================================
            // Push Field to Collection
            if ($field) {
                $collection->add($field);
            }
        }

        return $collection;
    }

    /**
     * Add a Field to Collection
     */
    public function add(AbstractField $field): self
    {
        $this->offsetSet((string) $field, $field);

        return $this;
    }

    /**
     * Get a Field from Collection
     */
    public function get(string $fieldId): ?AbstractField
    {
        if (!$this->offsetExists($fieldId)) {
            return null;
        }
        $field = $this->offsetGet($fieldId);
        if ($field instanceof AbstractField) {
            return $field;
        }

        return null;
    }

    /**
     * Check if a Field is Present on Collection
     */
    public function has(string $fieldId): bool
    {
        return !empty($this->get($fieldId));
    }

    /**
     * Remove a Field from Collection
     */
    public function remove(string $fieldId): self
    {
        if ($this->offsetExists($fieldId)) {
            $this->offsetUnset($fieldId);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $fields = array();

        foreach ($this as $field) {
            $fields[$field->getIdentifier()] = $field->toArray();
        }

        return $fields;
    }

    /**
     * @inheritDoc
     */
    public function reduce(): array
    {
        return array_keys($this->getArrayCopy());
    }

    /**
     * @inheritDoc
     */
    public function unique(): ?AbstractField
    {
        if (1 == $this->count()) {
            $this->rewind();
            $current = $this->current();

            return ($current instanceof AbstractField) ? $current : null;
        }

        return null;
    }

    /**
     * Check if All Collection Fields are Valid
     */
    public function isValid(): bool
    {
        return count($this) == count($this->filterValid());
    }

    /**
     * Merge Current Collection with another Field Collection
     */
    public function merge(FieldsCollectionInterface $fields): self
    {
        //====================================================================//
        // Walk on Fields to Merge
        foreach ($fields as $field) {
            //====================================================================//
            // Safety Check
            if (!$field instanceof AbstractField) {
                continue;
            }
            //====================================================================//
            // Ensure Field Exists
            $targetField = $this->get((string) $field) ?? $field;
            //====================================================================//
            // Merge Field Definition
            $targetField->update(array_filter($field->toArray()));
            $targetField->update(array(
                "read" => $field->isRead(),
                "write" => $field->isWrite(),
            ));
            if ($targetField->isValid()) {
                $this->offsetSet((string) $targetField, $targetField);
            }
        }

        return $this;
    }
}
