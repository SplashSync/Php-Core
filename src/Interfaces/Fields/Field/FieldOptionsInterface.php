<?php

namespace Splash\Core\Interfaces\Fields\Field;

/**
 * Interface for Splash Object Field Options
 */
interface FieldOptionsInterface
{
    /**
     * Set Field Possible Key/Value Choices
     */
    public function setChoices(array $choices): static;

    /**
     * Add Field Possible Key/Value Choice
     */
    public function addChoice(string $value, string $description): static;

    /**
     * Get Field Possible Key/Value Choices
     *
     * @return array<string, string>
     */
    public function getChoices(): array;

    /**
     * Configure for Multi-Lang
     *
     * @param null|string $isoCode Language ISO Code (i.e en_US | fr_FR)
     */
    public function setMultiLang(?string $isoCode, bool $isDefault): static;

    /**
     * Add a Field Option for Units Tests & More
     *
     * @param string                $key
     * @param scalar $value
     */
    public function addOption(string $key, $value = true): static;

    /**
     * Get Field Options for Units Tests & More
     *
     * @return array<string, scalar>
     */
    public function getOptions(): array;
}