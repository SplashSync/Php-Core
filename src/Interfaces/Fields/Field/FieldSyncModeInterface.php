<?php

namespace Splash\Core\Interfaces\Fields\Field;

/**
 * Interface for Splash Object Field Preferred Configuration
 */
interface FieldSyncModeInterface
{
    /**
     * Set Field Preferred Synchronisation Mode
     */
    public function setSyncMode(string $syncMode): static;

    /**
     * Get Field Preferred Synchronisation Mode
     */
    public function getSyncMode(): string;

    /**
     * Check Field is Prefer None
     * => No Synchronization for this field
     */
    public function isPreferNone(): bool;

    /**
     * Check Field is Prefer Read
     * => Only read data from current Server
     */
    public function isPreferRead(): bool;

    /**
     * Check Field is Prefer Write
     * => Only write data to current Server
     */
    public function isPreferWrite(): bool;

    /**
     * Check Field is Prefer Both
     * => Bi-Directional Synchronization if possible
     */
    public function isPreferBoth(): bool;
}