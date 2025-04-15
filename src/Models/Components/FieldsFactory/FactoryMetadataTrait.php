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

namespace Splash\Core\Models\Components\FieldsFactory;

use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\Fields\SplSyncMode;
use Splash\Core\Fields\ObjectField;

/**
 * Signify Server Current New Field Prefer Read & Write Mode
 */
trait FactoryMetadataTrait
{
    //==============================================================================
    //  FIELD SETUP - FAVORITE SYNC MODE
    //==============================================================================

    /**
     * Signify Server Current New Field Prefer Read & Write Mode
     */
    public function setPreferBoth(): static
    {
        $this->current()?->setSyncMode(SplSyncMode::BOTH);

        return $this;
    }

    /**
     * Signify Server Current New Field Prefer ReadOnly Mode
     */
    public function setPreferRead(): static
    {
        $this->current()?->setSyncMode(SplSyncMode::READ);

        return $this;
    }

    /**
     * Signify Server Current New Field Prefer WriteOnly Mode
     */
    public function setPreferWrite(): static
    {
        $this->current()?->setSyncMode(SplSyncMode::WRITE);

        return $this;
    }

    /**
     * Signify Server Current New Field Prefer No Sync Mode
     */
    public function setPreferNone(): static
    {
        $this->current()?->setSyncMode(SplSyncMode::NONE);

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - MICRODATA / AUTO MAPPING
    //==============================================================================

    /**
     * Update Current New Field set its meta information for auto-mapping
     *
     * @param string $itemType Field Microdata Type Url
     * @param string $itemProp Field Microdata Property Name
     *
     * @return $this
     */
    public function microData(string $itemType, string $itemProp): self
    {
        $this->current()?->setMicroData($itemType, $itemProp);

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - VALUES CHOICES
    //==============================================================================

    /**
     * Add Possible Choice to Current New Field (Translated)
     *
     * @param array $fieldChoices Possible Choice Array (Value => Description)
     */
    public function addChoices(array $fieldChoices): static
    {
        foreach ($fieldChoices as $value => $description) {
            $this->addChoice(
                (string) $value,
                (string) ($description ?? ucfirst($value) ?: ucfirst($value))
            );
        }

        return $this;
    }

    /**
     * Add Possible Choice to Current New Field Name (Translated)
     *
     * @param string $value       Possible Choice Value
     * @param string $description Choice Description for Display (Will Be Translated if Possible)
     */
    public function addChoice(string $value, string $description): static
    {
        $this->current()?->addChoice($value, Splash::trans(trim($description)));

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - LANGUAGES OPTIONS
    //==============================================================================

    /**
     * Select Default Language for Field List
     *
     * @param null|string $isoCode Language ISO Code (i.e en_US | fr_FR)
     */
    public function setDefaultLanguage(?string $isoCode): self
    {
        //====================================================================//
        // Store Default Language ISO Code with Safety Checks
        $this->dfLanguage = ObjectField::isValidIsoCode((string) $isoCode)
            ? $isoCode
            : $this->dfLanguage
        ;

        return $this;
    }

    /**
     * Check if ISO Code is Default Language
     *
     * @param null|string $isoCode Language ISO Code (i.e en_US | fr_FR)
     *
     * @return bool
     */
    public function isDefaultLanguage(?string $isoCode): bool
    {
        return (strtolower((string) $isoCode) == strtolower((string) $this->dfLanguage));
    }

    /**
     * Configure Current Field with Multi-Lang Options
     *
     * @param string $isoCode Language ISO Code (i.e en_US | fr_FR)
     */
    public function setMultiLang(string $isoCode): static
    {
        $this->current()?->setMultiLang(
            $isoCode,
            $this->isDefaultLanguage($isoCode)
        );

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - UNIT TEST OPTIONS
    //==============================================================================

    /**
     * Update Current New Field set list of associated fields
     */
    public function association(): static
    {
        $associations = array();
        foreach (func_get_args()?: array() as $association) {
            if (is_string($association)) {
                $associations[] = $association;
            }
        }
        $this->current()?->setAssociations($associations);

        return $this;
    }

    /**
     * Add New Options Array for Current Field
     *
     * @param array $fieldOptions Array of Options (Type => Value)
     */
    public function addOptions(array $fieldOptions): static
    {
        foreach ($fieldOptions as $type => $value) {
            $this->addOption($type, $value);
        }

        return $this;
    }

    /**
     * Add New Option for Current Field
     *
     * @param string                $key   Constrain Type
     * @param bool|float|int|string $value Constrain Value
     */
    public function addOption(string $key, $value = true): static
    {
        $this->current()?->addOption($key, $value);

        return $this;
    }
}
