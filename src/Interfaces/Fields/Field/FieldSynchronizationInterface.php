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
 * Interface for Splash Object Field features Flags Definition
 */
interface FieldSynchronizationInterface
{
    /**
     * Set Field Required Flag
     * Field is Required to Create a New Object (Bool)
     */
    public function setRequired(bool $required): self;

    /**
     * Get Field Required Flag
     * Field is Required to Create a New Object (Bool)
     */
    public function isRequired(): bool;

    /**
     * Set Field Readable Flag
     */
    public function setRead(bool $read): self;

    /**
     * Get Field Readable Flag
     */
    public function isRead(): bool;

    /**
     * Set Field Writable Flag
     */
    public function setWrite(bool $write): self;

    /**
     * Get Field Writable Flag
     */
    public function isWrite(): bool;

    /**
     * Set Field Should be Indexed Flag
     * Field Should be Indexed for Text Search (Bool)
     */
    public function setIndex(bool $index): self;

    /**
     * Set Field Should be Indexed Flag
     * Field Should be Indexed for Text Search (Bool)
     */
    public function isIndex(): bool;

    /**
     * Set Field Primary Flag
     */
    public function setPrimary(bool $primary): self;

    /**
     * Get Field Primary Flag
     */
    public function isPrimary(): bool;

    /**
     * Set Field Versioning / Archive Flag
     */
    public function setLogged(bool $logged): self;

    /**
     * Get Field Versioning / Archive Flag
     */
    public function isLogged(): bool;
}
