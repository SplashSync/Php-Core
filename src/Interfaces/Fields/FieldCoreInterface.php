<?php

namespace Splash\Core\Interfaces\Fields;

use Splash\Core\Helpers\StringConverter;

/**
 * Interface for Splash Object Field Definition
 */
interface FieldCoreInterface
{
    /**
     * Set Field Identifier
     */
    public function setIdentifier(string $id): static;

    /**
     * Get Field Identifier
     */
    public function getIdentifier(): string;

    /**
     * Set Field Name
     */
    public function setName(string $name): static;

    /**
     * Get Field Name
     */
    public function getName(): string;

    /**
     * Set Field Description
     */
    public function setDesc(string $desc): static;

    /**
     * Get Field Description
     */
    public function getDesc(): ?string;

    /**
     * Set Field Group Name
     */
    public function setGroup(string $group): static;

    /**
     * Get Field Group Name
     */
    public function getGroup(): ?string;
}