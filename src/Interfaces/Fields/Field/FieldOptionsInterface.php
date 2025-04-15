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

use Splash\Core\Interfaces\Fields\FieldInterface;

/**
 * Interface for Splash Object Field Options
 *
 * @phpstan-import-type RAW_CHOICE from FieldInterface
 */
interface FieldOptionsInterface
{
    /**
     * Set Field Possible Key/Value Choices
     */
    public function setChoices(array $choices): self;

    /**
     * Add Field Possible Key/Value Choice
     */
    public function addChoice(string $value, string $description): self;

    /**
     * Get Field Raw Possible Key/Value Choices
     *
     * @return array<int|string, RAW_CHOICE>
     */
    public function getRawChoices(): array;

    /**
     * Get Field Possible Key/Value Choices
     *
     * @return array<string, string>
     */
    public function getChoices(): array;

    /**
     * Configure for Multi-Lang
     *
     * @param null|string $isoCode Language ISO Code (i.e en_US | fr_FR)
     */
    public function setMultiLang(?string $isoCode, bool $isDefault): self;

    /**
     * Add a Field Option for Units Tests & More
     *
     * @param string $key
     * @param scalar $value
     */
    public function addOption(string $key, $value = true): self;

    /**
     * Get Field Options for Units Tests & More
     *
     * @return array<string, scalar>
     */
    public function getOptions(): array;
}
