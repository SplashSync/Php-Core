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

use CallbackFilterIterator;
use Splash\Core\Interfaces\Fields\FieldsCollectionInterface;
use Splash\Core\Models\Fields\AbstractField;
use Splash\Core\Models\Fields\Collections\FilteringTrait;
use Splash\Core\Models\Fields\Collections\FinderTrait;

/**
 * Automated Storage Class for Objects Fields Collection
 */
class FilteredCollection extends CallbackFilterIterator implements FieldsCollectionInterface
{
    use FilteringTrait;
    use FinderTrait;

    /**
     * Count Number of Available Fields
     */
    public function count():int
    {
        $count = 0;
        foreach ($this as $value) {
            if ($value instanceof AbstractField) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function get(string $fieldId): ?AbstractField
    {
        foreach ($this as $value) {
            if (!$value instanceof AbstractField) {
                continue;
            }
            if ($fieldId == $value->getIdentifier()) {
                return $value;
            }
        }

        return null;
    }

    /**
     * @inheritDoc
     */
    public function has(string $fieldId): bool
    {
        return !empty($this->get($fieldId));
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
     * @inheritDoc
     */
    public function reduce(): array
    {
        $keys = array();

        foreach ($this as $value) {
            if ($value instanceof AbstractField) {
                $keys[] = $value->getIdentifier();
            }
        }

        return $keys;
    }
}
