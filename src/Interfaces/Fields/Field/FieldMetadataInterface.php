<?php

namespace Splash\Core\Interfaces\Fields\Field;

/**
 * Interface for Splash Object Field Metadata Definition
 */
interface FieldMetadataInterface
{
    /**
     * Set Metadata for Auto-Mapping
     */
    public function setMicroData(string $itemType, string $itemProp): static;

    /**
     * Get Tag from Metadata
     */
    public static function toTag(string $itemType, string $itemProp): string;

    /**
     * Get Metadata Type
     */
    public function getItemType(): string;

    /**
     * Get Metadata Prop
     */
    public function getItemProp(): string;

    /**
     * Set Field Auto-Mapping Tag
     */
    public function setTag(?string $tag): static;

    /**
     * Get Field Auto-Mapping Tag
     */
    public function getTag(): string;

    /**
     * Has Field Auto-Mapping Tag
     */
    public function isTagged(): bool;
}