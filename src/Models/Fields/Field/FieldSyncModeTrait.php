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

namespace Splash\Core\Models\Fields\Field;

use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\Fields\SplFieldProps as Props;
use Splash\Core\Dictionary\Fields\SplSyncMode;

/**
 * Splash Object Field Preferred Configuration
 */
trait FieldSyncModeTrait
{
    /**
     * Object Field Preferred Synchronization Mode
     */
    private string $syncMode = SplSyncMode::BOTH;

    /**
     * @inheritDoc
     */
    public function setSyncMode(string $syncMode): static
    {
        if (!SplSyncMode::isValid($syncMode)) {
            Splash::log()->err("Invalid Sync Mode for Field ".$this->getName());
        } else {
            $this->syncMode = $syncMode;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSyncMode(): string
    {
        return $this->syncMode;
    }

    /**
     * @inheritDoc
     */
    public function isPreferNone(): bool
    {
        return SplSyncMode::NONE == $this->syncMode;
    }

    /**
     * @inheritDoc
     */
    public function isPreferRead(): bool
    {
        return SplSyncMode::READ == $this->syncMode;
    }

    /**
     * @inheritDoc
     */
    public function isPreferWrite(): bool
    {
        return SplSyncMode::WRITE == $this->syncMode;
    }

    /**
     * @inheritDoc
     */
    public function isPreferBoth(): bool
    {
        return SplSyncMode::BOTH == $this->syncMode;
    }

    /**
     * Import / Override Field SyncMode Definition
     *
     * @param array<string, mixed> $values Custom Values to Write
     */
    protected function updateSyncModeValues(array $values): static
    {
        $this->updateStringValue($values, Props::SYNC_MODE, fn ($value) => $this->setSyncMode($value));

        return $this;
    }
}
