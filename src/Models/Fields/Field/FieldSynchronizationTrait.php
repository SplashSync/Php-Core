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

/**
 * Splash Object Field Features Flags Definition
 */
trait FieldSynchronizationTrait
{
    /**
     * Field Required Flag
     */
    private bool $required = false;

    /**
     * Field Read Flag
     */
    private bool $read = true;

    /**
     * Field Write Flag
     */
    private bool $write = true;

    /**
     * Field Indexed Flag
     */
    private bool $index = false;

    /**
     * Field Primary Flag
     */
    private bool $primary = false;

    /**
     * Field Log / Archive Flag
     */
    private bool $log = false;

    /**
     * @inheritDoc
     */
    public function setRequired(bool $required): static
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * @inheritDoc
     */
    public function setRead(bool $read): static
    {
        $this->read = $read;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRead(): bool
    {
        return $this->read;
    }

    /**
     * @inheritDoc
     */
    public function setWrite(bool $write): static
    {
        $this->write = $write;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isWrite(): bool
    {
        return $this->write;
    }

    /**
     * @inheritDoc
     */
    public function setIndex(bool $index): static
    {
        $this->index = $index;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isIndex(): bool
    {
        return $this->index;
    }

    /**
     * @inheritDoc
     */
    public function setPrimary(bool $primary): static
    {
        //====================================================================//
        // Safety Checks ==> Verify Field Type is Allowed
        $fieldType = $this->getListFieldType() ?? $this->getType();
        if ($primary && !self::isValidPrimaryType($fieldType)) {
            Splash::log()->err(
                sprintf("Primary flag is not allowed for type %s", $fieldType)
            );

            return $this;
        }
        $this->primary = $primary;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPrimary(): bool
    {
        return $this->primary;
    }

    /**
     * @inheritDoc
     */
    public function setLogged(bool $logged): static
    {
        $this->log = $logged;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isLogged(): bool
    {
        return $this->log;
    }

    //==============================================================================
    // Data Imports Management
    //==============================================================================

    /**
     * Import / Override Field Synchronization Definition
     *
     * @param array<string, mixed> $values Custom Values to Write
     */
    protected function updateSynchronizationValues(array $values): static
    {
        $this->updateBooleanValue($values, Props::REQUIRED, fn ($value) => $this->setRequired($value));
        $this->updateBooleanValue($values, Props::READ, fn ($value) => $this->setRead($value));
        $this->updateBooleanValue($values, Props::WRITE, fn ($value) => $this->setWrite($value));
        $this->updateBooleanValue($values, Props::INDEX, fn ($value) => $this->setIndex($value));
        $this->updateBooleanValue($values, Props::PRIMARY, fn ($value) => $this->setPrimary($value));
        $this->updateBooleanValue($values, Props::LOG, fn ($value) => $this->setLogged($value));

        return $this;
    }
}
