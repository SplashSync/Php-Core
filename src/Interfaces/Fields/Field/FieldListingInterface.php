<?php

namespace Splash\Core\Interfaces\Fields\Field;

/**
 * Interface for Splash Object Field Listing Configuration
 */
interface FieldListingInterface
{
    /**
     * Set Field is In Object List Flag
     * This field is available in Objects List.
     */
    public function setListed(bool $listed): static;

    /**
     * Get Field is In Object List Flag
     * This field is available in Objects List.
     */
    public function isListed(): bool;

    /**
     * Set Field In Hidden Object List Flag
     *
     * This field is in Objects List but Hidden.
     * This improves reading of lists, but makes field usable for analyzes.
     */
    public function setListHidden(bool $listHidden): static;

    /**
     * Get Field In Hidden Object List Flag
     */
    public function isListHidden(): bool;
}