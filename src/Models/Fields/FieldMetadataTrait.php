<?php

namespace Splash\Core\Models\Fields;

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
     *
     * @param string $itemType
     * @param string $itemProp
     *
     * @return self
     */
    public function setMicroData(string $itemType, string $itemProp): static
    {
        if (!$itemType && !$itemProp) {
            $this->itemType = null;
            $this->itemProp = null;
            $this->tag = null;
        } else {
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
    public function getItemType(): string
    {
        return $this->itemType;
    }

    /**
     * @inheritDoc
     */
    public function getItemProp(): string
    {
        return $this->itemProp;
    }

    /**
     * @inheritDoc
     */
    private function setTag(string $tag): static
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTag(): string
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


}