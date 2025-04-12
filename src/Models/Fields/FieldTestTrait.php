<?php

namespace Splash\Core\Models\Fields;

/**
 * Interface for Splash Object Field Test Options
 */
trait FieldTestTrait
{
    /**
     * Associated Field. Fields to Generate with this field.
     *
     * @var string[]
     */
    private array $associations = array();

    /**
     * Do No Perform Tests for this Field
     */
    private bool $noTests = false;

    /**
     * @inheritDoc
     */
    public function addAssociation(array $fieldId): static
    {
        $this->associations[] = $fieldId;
        $this->associations = array_unique($this->associations);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAssociations(array $fieldIds): static
    {
        $this->associations = array_unique($fieldIds);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAssociations(): array
    {
        return $this->associations;
    }

    /**
     * @inheritDoc
     */
    public function setNotTested(bool $noTests): static
    {
        $this->noTests = $noTests;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isNotTested(): bool
    {
        return $this->noTests;
    }
}