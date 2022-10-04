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

namespace   Splash\Components;

use Splash\Core\SplashCore      as Splash;
use Splash\Models\AbstractConfigurator;
use Splash\Models\Fields\ObjectField;

/**
 * This Class is a Generator for Objects Fields Definition
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FieldsFactory
{
    //==============================================================================
    //  Favorites Sync Modes
    //==============================================================================

    const MODE_BOTH = ObjectField::MODE_BOTH;
    const MODE_READ = ObjectField::MODE_READ;
    const MODE_WRITE = ObjectField::MODE_WRITE;
    const MODE_NONE = ObjectField::MODE_NONE;

    //==============================================================================
    //  Meta Data Access MicroDatas
    //==============================================================================

    const META_URL = ObjectField::META_URL;                             // Splash Specific Schemas Url.
    const META_OBJECTID = ObjectField::META_OBJECTID;                   // Splash Object Id.
    const META_DATECREATED = ObjectField::META_DATECREATED;             // Splash Object Create Date.
    const META_ORIGIN_NODE_ID = ObjectField::META_ORIGIN_NODE_ID;       // Object Source Server Identifier
    const META_ORIGIN_NODE_NAME = ObjectField::META_ORIGIN_NODE_NAME;   // Object Source Server Name

    //====================================================================//
    // Data Storage
    //====================================================================//

    /**
     * New Object Field Storage
     *
     * @var null|ObjectField
     */
    private ?ObjectField $new;

    /**
     * Object Fields List Storage
     *
     * @var ObjectField[]
     */
    private array $fields = array();

    /**
     * Fields Default Language
     *
     * @var null|string
     */
    private ?string $dfLanguage;

    /**
     * Fields Configurators
     *
     * @var array
     */
    private array $configurators = array();

    //====================================================================//
    //  FIELDS :: DATA TYPES DEFINITION
    //====================================================================//

    /**
     * Create a new Field Definition with default parameters
     *
     * @param string      $fieldType Standard Data Type (Refer Splash.Inc.php)
     * @param null|string $fieldId   Local Data Identifier (Shall be unik on local machine)
     * @param null|string $fieldName Data Name (Will Be Translated by Splash if Possible)
     *
     * @return $this
     */
    public function create(string $fieldType, string $fieldId = null, string $fieldName = null): self
    {
        //====================================================================//
        // Commit Last Created if not already done
        if (isset($this->new)) {
            $this->commit();
        }
        //====================================================================//
        // Create new empty field
        $this->new = new ObjectField($fieldType);
        //====================================================================//
        // Set Field Identifier
        if ($fieldId) {
            $this->new->setIdentifier($fieldId);
        }
        //====================================================================//
        // Set Field Name
        if ($fieldName) {
            $this->new->setName($fieldName);
        }

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - CORE INFOS
    //==============================================================================

    /**
     * Set Current New Field Identifier
     *
     * @param string $fieldId Local Data Identifier (Must be unique on local machine)
     *
     * @return $this
     */
    public function identifier(string $fieldId): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setIdentifier($fieldId);
        }

        return $this;
    }

    /**
     * Set Current New Field Name
     *
     * @param string $fieldName Data Name
     *
     * @return $this
     */
    public function name(string $fieldName): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setName($fieldName);
            if (!$this->new->hasDesc()) {
                $this->description($fieldName);
            }
        }

        return $this;
    }

    /**
     * Update Current New Field with descriptions (Translated)
     *
     * @param string $fieldDesc Data Description (Will Be Translated if Possible)
     *
     * @return $this
     */
    public function description(string $fieldDesc): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setDesc(Splash::trans(trim($fieldDesc)));
        }

        return $this;
    }

    /**
     * Update Current New Field with Field Group Name (Translated)
     *
     * @param string $fieldGroup Data Group (Will Be Translated if Possible)
     *
     * @return $this
     */
    public function group(string $fieldGroup): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setGroup(Splash::trans(trim($fieldGroup)));
        }

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - CORE FLAGS
    //==============================================================================

    /**
     * Update Current New Field set as required for creation
     *
     * @param null|bool $isRequired
     *
     * @return $this
     */
    public function isRequired(?bool $isRequired = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setRequired((bool) $isRequired);
        }

        return $this;
    }

    /**
     * Update Current New Field set as primary key
     *
     * @param null|bool $isPrimary
     *
     * @return $this
     */
    public function isPrimary(?bool $isPrimary = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setPrimary((bool) $isPrimary);
        }

        return $this;
    }

    /**
     * Update Current New Field set as indexed
     *
     * @param null|bool $isIndexed
     *
     * @return $this
     */
    public function isIndexed(?bool $isIndexed = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setIndex((bool) $isIndexed);
        }

        return $this;
    }

    /**
     * Update Current New Field set as Read Only Field
     *
     * @param null|bool $isReadOnly
     *
     * @return $this
     */
    public function isReadOnly(?bool $isReadOnly = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } elseif ($isReadOnly) {
            //====================================================================//
            // Update New Field structure
            $this->new->setRead(true);
            $this->new->setWrite(false);
        }

        return $this;
    }

    /**
     * Update Current New Field set as Write Only Field
     *
     * @param null|bool $isWriteOnly
     *
     * @return $this
     */
    public function isWriteOnly(?bool $isWriteOnly = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } elseif ($isWriteOnly) {
            //====================================================================//
            // Update New Field structure
            $this->new->setRead(false);
            $this->new->setWrite(true);
        }

        return $this;
    }

    /**
     * Update Current New Field set as available in Objects List
     *
     * @param null|bool $isListed
     *
     * @return $this
     */
    public function isListed(?bool $isListed = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setIsListed((bool) $isListed);
        }

        return $this;
    }

    /**
     * Set Field In Hidden Object List Flag
     *
     * This field is in Objects List but Hidden.
     * This improves reading of lists, but makes field usable for analyzes.
     *
     * @param null|bool $hListed
     *
     * @return $this
     */
    public function isListHidden(?bool $hListed = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setHiddenList((bool) $hListed);
        }

        return $this;
    }

    /**
     * Update Current New Field set as recommended for logging
     *
     * @param null|bool $isLogged
     *
     * @return $this
     */
    public function isLogged(?bool $isLogged = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setIsLogged((bool) $isLogged);
        }

        return $this;
    }

    /**
     * Update Current New Field to Set Field Excluded from General Unit Tests
     * May be tested by Custom Tests Suites
     *
     * @param null|bool $isNoTest
     *
     * @return $this
     */
    public function isNotTested(?bool $isNoTest = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setIsNotTested((bool) $isNoTest);
        }

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - LIST INFOS
    //==============================================================================

    /**
     * Update Current New Field set as it inside a list
     *
     * @param string $listName Name of List
     *
     * @return $this
     */
    public function inList(string $listName): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Push Field to List
            $this->new->setInlist($listName);
        }

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - FAVORITE SYNC MODE
    //==============================================================================

    /**
     * Signify Server Current New Field Prefer ReadOnly Mode
     *
     * @return $this
     */
    public function setPreferRead(): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setSyncMode(self::MODE_READ);
        }

        return $this;
    }

    /**
     * Signify Server Current New Field Prefer WriteOnly Mode
     *
     * @return $this
     */
    public function setPreferWrite(): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setSyncMode(self::MODE_WRITE);
        }

        return $this;
    }

    /**
     * Signify Server Current New Field Prefer No Sync Mode
     *
     * @return $this
     */
    public function setPreferNone(): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setSyncMode(self::MODE_NONE);
        }

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
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setMicroData($itemType, $itemProp);
        }

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - VALUES CHOICES
    //==============================================================================

    /**
     * Add Possible Choice to Current New Field (Translated)
     *
     * @param array $fieldChoices Possible Choice Array (Value => Description)
     *
     * @return $this
     */
    public function addChoices(array $fieldChoices): self
    {
        foreach ($fieldChoices as $value => $description) {
            $this->addChoice($value, $description);
        }

        return $this;
    }

    /**
     * Add Possible Choice to Current New Field Name (Translated)
     *
     * @param string $value       Possible Choice Value
     * @param string $description Choice Description for Display (Will Be Translated if Possible)
     *
     * @return $this
     */
    public function addChoice(string $value, string $description): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->addChoice($value, Splash::trans(trim($description)));
        }

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - LANGUAGES OPTIONS
    //==============================================================================

    /**
     * Select Default Language for Field List
     *
     * @param null|string $isoCode Language ISO Code (i.e en_US | fr_FR)
     *
     * @return $this
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
     *
     * @return $this
     */
    public function setMultiLang(string $isoCode): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->setMultiLang(
                $isoCode,
                $this->isDefaultLanguage($isoCode)
            );
        }

        return $this;
    }

    //==============================================================================
    //  FIELD SETUP - UNIT TEST OPTIONS
    //==============================================================================

    /**
     * Update Current New Field set list of associated fields
     *
     * @return $this
     */
    public function association(): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Set Field Associations
            $this->new->setAssociation(func_get_args() ?: array());
        }

        return $this;
    }

    /**
     * Add New Options Array for Current Field
     *
     * @param array $fieldOptions Array of Options (Type => Value)
     *
     * @return $this
     */
    public function addOptions(array $fieldOptions): self
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
     *
     * @return $this
     */
    public function addOption(string $key, $value = true): self
    {
        //====================================================================//
        // Safety Checks ==> Verify a new Field Exists
        if (empty($this->new)) {
            Splash::log()->err("ErrFieldsNoNew");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->new->addOption($key, $value);
        }

        return $this;
    }

    //====================================================================//
    //  FIELDS - LIST MANAGEMENT
    //====================================================================//

    /**
     * Get Current New Field
     *
     * @return null|ObjectField
     */
    public function current(): ?ObjectField
    {
        return $this->new;
    }

    /**
     * Check if Field Id is Defined
     *
     * @param string $fieldId
     *
     * @return bool
     */
    public function has(string $fieldId): bool
    {
        return isset($this->fields[$fieldId]);
    }

    /**
     * Check if Field Id is Defined
     *
     * @param string $fieldId
     *
     * @return null|ObjectField
     */
    public function get(string $fieldId): ?ObjectField
    {
        return $this->fields[$fieldId] ?? null;
    }

    /**
     * Save Current New Field in list & Clean current new field
     *
     * @return null|array<int, array>
     *
     * @phpstan-return  null|array<int, array{
     *      type: string,
     *      id: string,
     *      name: string,
     *      desc: string,
     *      group: string,
     *      required: null|bool|string,
     *      read: null|bool|string,
     *      write: null|bool|string,
     *      inlist: null|bool|string,
     *      hlist: null|bool|string,
     *      log: null|bool|string,
     *      notest: null|bool|string,
     *      primary: null|bool|string,
     *      syncmode: string,
     *      itemprop: null|string,
     *      itemtype: null|string,
     *      tag: null|string,
     *      choices: null|array{ key: string, value: scalar},
     *      asso: null|string[],
     *      options: array<string, scalar>
     * }>
     */
    public function publish(): ?array
    {
        //====================================================================//
        // Commit Last Created if not already done
        $this->commit();
        //====================================================================//
        // Safety Checks
        if (empty($this->fields)) {
            return Splash::log()->errNull("ErrFieldsNoList");
        }

        //====================================================================//
        // Convert Fields To Array
        $buffer = array();
        foreach ($this->fields as $field) {
            $buffer[] = $field->toArray();
        }
        //====================================================================//
        // Execute Configurators on Fields
        $configuredBuffer = $this->executeConfigurators($buffer);
        //====================================================================//
        // Reset Fields Factory
        $this->reset();
        //====================================================================//
        // Execute Configurators on Fields
        return $configuredBuffer;
    }

    //====================================================================//
    //  FIELDS - CONFIGURATORS MANAGEMENT
    //====================================================================//

    /**
     * Register a Configurator for Fields Override
     *
     * @param string               $objectType
     * @param AbstractConfigurator $configurator
     *
     * @return self
     */
    public function registerConfigurator(string $objectType, AbstractConfigurator $configurator): self
    {
        $this->configurators[] = array(
            "objectType" => $objectType,
            "configurator" => $configurator,
        );

        return $this;
    }

    //====================================================================//
    //  FIELDS - PRIVATE METHODS
    //====================================================================//

    /**
     * Save Current New Field in list & Clean current new field
     *
     * @return void
     */
    private function commit(): void
    {
        //====================================================================//
        // Safety Checks
        if (!isset($this->new)) {
            return;
        }
        //====================================================================//
        // Validate Current New Field
        if (!$this->new->validate()) {
            $this->new = null;

            return;
        }
        //====================================================================//
        // Insert Field List if Valid
        $this->fields[$this->new->getIdentifier()] = $this->new;
        $this->new = null;
    }

    /**
     * Reset Field Factory
     */
    private function reset(): void
    {
        $this->new = null;
        $this->fields = array();
        $this->dfLanguage = null;
        $this->configurators = array();
    }

    /**
     * Register a Configurator for Fields Override
     *
     * @param array $fields
     *
     * @return array
     *
     * @phpstan-return  array<int, array{
     *      type: string,
     *      id: string,
     *      name: string,
     *      desc: string,
     *      group: string,
     *      required: null|bool|string,
     *      read: null|bool|string,
     *      write: null|bool|string,
     *      index: null|bool|string,
     *      inlist: null|bool|string,
     *      hlist: null|bool|string,
     *      log: null|bool|string,
     *      notest: null|bool|string,
     *      primary: null|bool|string,
     *      syncmode: string,
     *      itemprop: null|string,
     *      itemtype: null|string,
     *      tag: null|string,
     *      choices: null|array{ key: string, value: scalar},
     *      asso: null|string[],
     *      options: array<string, scalar>
     * }>
     */
    private function executeConfigurators(array $fields): array
    {
        foreach ($this->configurators as $sequence) {
            if (($sequence['configurator'] instanceof AbstractConfigurator) && is_string($sequence['objectType'])) {
                $fields = $sequence['configurator']->overrideFields($sequence['objectType'], $fields);
            }
        }

        return $fields;
    }
}
