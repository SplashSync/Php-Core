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

/**
 * Trait FactoryCoreTrait
 *
 * Provides methods to define and configure the properties, behavior, and rules of fields in the context of
 * object creation, validation, and field setup. It allows defining field identifiers, names, groups, descriptions,
 * and various flags related to their usage, visibility, and inclusion in logs, lists, or tests.
 */
trait FactoryCoreTrait
{
    //==============================================================================
    //  FIELD SETUP - CORE INFOS
    //==============================================================================

    /**
     * Set Current New Field Identifier
     *
     * @param string $fieldId Local Data Identifier (Must be unique on local machine)
     */
    public function identifier(string $fieldId): static
    {
        $this->current()?->setIdentifier($fieldId);

        return $this;
    }

    /**
     * Set Current New Field Name
     *
     * @param string $fieldName Data Name
     */
    public function name(string $fieldName): static
    {
        $this->current()?->setName($fieldName);

        return $this;
    }

    /**
     * Update Current New Field with descriptions (Translated)
     *
     * @param string $fieldDesc Data Description (Will Be Translated if Possible)
     */
    public function description(string $fieldDesc): static
    {
        $this->current()?->setDesc(Splash::trans(trim($fieldDesc)));

        return $this;
    }

    /**
     * Update Current New Field with Field Group Name (Translated)
     *
     * @param string $fieldGroup Data Group (Will Be Translated if Possible)
     */
    public function group(string $fieldGroup): static
    {
        $this->current()?->setGroup(Splash::trans(trim($fieldGroup)));

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - CORE FLAGS
    //==============================================================================

    /**
     * Update Current New Field set as required for creation
     */
    public function isRequired(?bool $isRequired = true): static
    {
        $this->current()?->setRequired((bool) $isRequired);

        return $this;
    }

    /**
     * Update Current New Field set as primary key
     */
    public function isPrimary(?bool $isPrimary = true): static
    {
        $this->current()?->setPrimary((bool) $isPrimary);

        return $this;
    }

    /**
     * Update Current New Field set as indexed
     */
    public function isIndexed(?bool $isIndexed = true): static
    {
        $this->current()?->setIndex((bool) $isIndexed);

        return $this;
    }

    /**
     * Update Current New Field set as Read Only Field
     */
    public function isReadOnly(?bool $isReadOnly = true): static
    {
        if ($isReadOnly) {
            $this->current()?->setRead(true)->setWrite(false);
        }

        return $this;
    }

    /**
     * Update Current New Field set as Write Only Field
     */
    public function isWriteOnly(?bool $isWriteOnly = true): static
    {
        if ($isWriteOnly) {
            $this->current()?->setRead(false)->setWrite(true);
        }

        return $this;
    }

    /**
     * Update Current New Field set as available in Objects List
     */
    public function isListed(?bool $isListed = true): static
    {
        $this->current()?->setListed((bool) $isListed);

        return $this;
    }

    /**
     * Set Field In Hidden Object List Flag
     *
     * This field is in Objects List but Hidden.
     * This improves reading of lists, but makes field usable for analyzes.
     *
     * @param null|bool $listHidden
     *
     * @return $this
     */
    public function isListHidden(?bool $listHidden = true): static
    {
        $this->current()?->setListHidden((bool) $listHidden);

        return $this;
    }

    /**
     * Update Current New Field set as recommended for logging
     */
    public function isLogged(?bool $isLogged = true): static
    {
        $this->current()?->setLogged((bool) $isLogged);

        return $this;
    }

    /**
     * Update Current New Field to Set Field Excluded from General Unit Tests
     * May be tested by Custom Tests Suites
     */
    public function isNotTested(?bool $isNoTest = true): static
    {
        $this->current()?->setNotTested((bool) $isNoTest);

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - LIST INFOS
    //==============================================================================

    /**
     * Update Current New Field set as it inside a list
     */
    public function inList(string $listName): static
    {
        $this->current()?->setInlist($listName);

        return $this;
    }
}
