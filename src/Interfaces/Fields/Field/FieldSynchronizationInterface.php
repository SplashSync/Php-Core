<?php

namespace Splash\Core\Interfaces\Fields\Field;

/**
 * Interface for Splash Object Field features Flags Definition
 */
interface FieldSynchronizationInterface
{
    /**
     * Set Field Required Flag
     * Field is Required to Create a New Object (Bool)
     */
    public function setRequired(bool $required): static;

    /**
     * Get Field Required Flag
     * Field is Required to Create a New Object (Bool)
     */
    public function isRequired(): bool;

    /**
     * Set Field Readable Flag
     */
    public function setRead(bool $read): static;

    /**
     * Get Field Readable Flag
     */
    public function isRead(): bool;

    /**
     * Set Field Writable Flag
     */
    public function setWrite(bool $write): static;

    /**
     * Get Field Writable Flag
     */
    public function isWrite(): bool;

    /**
     * Set Field Should be Indexed Flag
     * Field Should be Indexed for Text Search (Bool)
     */
    public function setIndex(bool $index): static;

    /**
     * Set Field Should be Indexed Flag
     * Field Should be Indexed for Text Search (Bool)
     */
    public function isIndex(): bool;

    /**
     * Set Field Primary Flag
     */
    public function setPrimary(bool $primary): static;

    /**
     * Get Field Primary Flag
     */
    public function isPrimary(): bool;

    /**
     * Set Field Versioning / Archive Flag
     */
    public function setLogged(bool $logged): static;

    /**
     * Get Field Versioning / Archive Flag
     */
    public function isLogged(): bool;
}