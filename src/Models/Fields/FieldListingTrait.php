<?php

namespace Splash\Core\Models\Fields;

/**
 * Splash Object Field Listing Configuration
 */
trait FieldListingTrait
{
    /**
     * Field Required Flag
     */
    private bool $listed = false;

    /**
     * Field In Hidden Object List Flag
     */
    private bool $listHidden = false;

    /**
     * @inheritDoc
     */
    public function setListed(bool $listed): self
    {
        $this->listed = $listed;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isListed(): bool
    {
        return $this->listed;
    }

    /**
     * @inheritDoc
     */
    public function setListHidden(bool $listHidden): self
    {
        $this->listHidden = $listHidden;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isListHidden(): bool
    {
        return $this->listHidden;
    }
}