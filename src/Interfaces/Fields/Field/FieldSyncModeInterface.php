<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Core\Interfaces\Fields\Field;

/**
 * Interface for Splash Object Field Preferred Configuration
 */
interface FieldSyncModeInterface
{
    /**
     * Set Field Preferred Synchronisation Mode
     */
    public function setSyncMode(string $syncMode): self;

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
