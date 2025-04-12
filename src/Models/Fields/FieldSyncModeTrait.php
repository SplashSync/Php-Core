<?php

namespace Splash\Core\Models\Fields;

use Splash\Core\Client\Splash;
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
    public function setSyncMode(string $syncMode): self
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
        return $this->syncMode == SplSyncMode::NONE;
    }

    /**
     * @inheritDoc
     */
    public function isPreferRead(): bool
    {
        return $this->syncMode == SplSyncMode::READ;
    }

    /**
     * @inheritDoc
     */
    public function isPreferWrite(): bool
    {
        return $this->syncMode == SplSyncMode::WRITE;
    }

    /**
     * @inheritDoc
     */
    public function isPreferBoth(): bool
    {
        return $this->syncMode == SplSyncMode::BOTH;
    }
}