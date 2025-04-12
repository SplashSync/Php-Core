<?php

namespace Splash\Core\Interfaces\Fields\Field;

/**
 * Interface for Splash Object Field Test Options
 */
interface FieldTestInterface
{
    /**
     * Add Associated Field. Fields to Generate with this field.
     */
    public function addAssociation(array $fieldId): static;

    /**
     * Set Associated Fields. Fields to Generate with this field.
     *
     * @param string[] $fieldIds
     *
     */
    public function setAssociations(array $fieldIds): static;

    /**
     * Get Associated Fields. Fields to Generate with this field.
     *
     * @return string[]
     */
    public function getAssociations(): array;

    /**
     * Set Field Not Tested Flag
     * Do No Perform Set Tests for this Field
     */
    public function setNotTested(bool $noTests): static;

    /**
     * Get Field Not Tested Flag
     * Do No Perform Set Tests for this Field
     */
    public function isNotTested(): bool;
}