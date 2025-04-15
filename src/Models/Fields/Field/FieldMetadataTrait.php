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

namespace Splash\Core\Models\Fields\Field;

use Splash\Core\Dictionary\Fields\SplFieldProps as Props;
use Splash\Core\Helpers\ObjectsHelper;

/**
 * Interface for Splash Object Field Metadata Definition
 */
trait FieldMetadataTrait
{
    /**
     * Metadata Type for Auto-Mapping
     */
    private ?string $itemType = null;

    /**
     * Metadata Prop for Auto-Mapping
     */
    private ?string $itemProp = null;

    /**
     * Metadata Tag for Auto-Mapping
     */
    private ?string $tag = null;

    /**
     * Set Metadata for Auto-Mapping
     */
    public function setMicroData(string $itemType, string $itemProp): static
    {
        if (!$itemType && !$itemProp) {
            $this->itemType = null;
            $this->itemProp = null;
            $this->tag = null;

            return $this;
        }
        $itemProp = $itemProp ?: $this->itemProp;
        $itemType = $itemType ?: $this->itemType;
        if ($itemType && $itemProp) {
            $this->itemProp = $itemProp;
            $this->itemType = $itemType;
            $this->tag = self::toTag($itemType, $itemProp);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public static function toTag(string $itemType, string $itemProp): string
    {
        return md5((string) ObjectsHelper::encode($itemType, $itemProp));
    }

    /**
     * @inheritDoc
     */
    public function getItemType(): ?string
    {
        return $this->itemType;
    }

    /**
     * @inheritDoc
     */
    public function getItemProp(): ?string
    {
        return $this->itemProp;
    }

    /**
     * @inheritDoc
     */
    public function setTag(?string $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTag(): ?string
    {
        return $this->tag;
    }

    /**
     * @inheritDoc
     */
    public function isTagged(): bool
    {
        return !empty($this->tag);
    }

    /**
     * Import / Override Field Metadata Definition
     *
     * @param array<string, mixed> $values Custom Values to Write
     */
    protected function updateMetadataValues(array $values): static
    {
        //==============================================================================
        // Get Updated Field Metadata
        $itemType = $values[Props::MICRODATA_URL] ?? $this->getItemType();
        $itemProp = $values[Props::MICRODATA_PROP] ?? $this->getItemProp();
        if ($itemType && $itemProp && is_string($itemType) && is_string($itemProp)) {
            //==============================================================================
            // Check if Metadata Updated
            if ($itemType != $this->getItemType() || $itemProp != $this->getItemProp()) {
                $this->setMicroData($itemType, $itemProp);
            }
        } else {
            //==============================================================================
            // Empty Metadata => Update Field Tag Only
            $this->updateStringValue($values, Props::TAG, fn ($value) => $this->setTag($value));
        }

        return $this;
    }
}
