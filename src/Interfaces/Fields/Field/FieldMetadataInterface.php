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

namespace Splash\Core\Interfaces\Fields\Field;

/**
 * Interface for Splash Object Field Metadata Definition
 */
interface FieldMetadataInterface
{
    /**
     * Set Metadata for Auto-Mapping
     */
    public function setMicroData(string $itemType, string $itemProp): self;

    /**
     * Get Tag from Metadata
     */
    public static function toTag(string $itemType, string $itemProp): string;

    /**
     * Get Metadata Type
     */
    public function getItemType(): ?string;

    /**
     * Get Metadata Prop
     */
    public function getItemProp(): ?string;

    /**
     * Set Field Auto-Mapping Tag
     */
    public function setTag(?string $tag): self;

    /**
     * Get Field Auto-Mapping Tag
     */
    public function getTag(): ?string;

    /**
     * Has Field Auto-Mapping Tag
     */
    public function isTagged(): bool;
}
