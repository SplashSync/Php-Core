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

namespace Splash\Core\Components;

use Splash\Core\Client\Splash;
use Splash\Core\Fields\ObjectField;
use Splash\Core\Interfaces\Fields\FieldInterface;
use Splash\Core\Models\Components\FieldsFactory\CollectionTrait;
use Splash\Core\Models\Components\FieldsFactory\ConfiguratorsTrait;
use Splash\Core\Models\Components\FieldsFactory\FactoryCoreTrait;
use Splash\Core\Models\Components\FieldsFactory\FactoryMetadataTrait;
use Splash\Core\Models\Components\FieldsFactory\FactoryTemplateTrait;

/**
 * This Class is a Generator for Objects Fields Definition
 *
 * @phpstan-import-type FIELD from FieldInterface
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class FieldsFactory
{
    use FactoryCoreTrait;
    use FactoryMetadataTrait;
    use FactoryTemplateTrait;
    use CollectionTrait;
    use ConfiguratorsTrait;

    /**
     * Fields Default Language
     *
     * @var null|string
     */
    private ?string $dfLanguage = null;

    //====================================================================//
    //  FIELDS :: DATA TYPES DEFINITION
    //====================================================================//

    /**
     * Create a new Field Definition with default parameters
     *
     * @param string      $fieldType Standard Data Type
     * @param null|string $fieldId   Local Data Identifier (Shall be unique on local machine)
     * @param null|string $fieldName Data Name (Will Be Translated by Splash if Possible)
     */
    public function create(string $fieldType, ?string $fieldId = null, ?string $fieldName = null): self
    {
        //====================================================================//
        // Commit Last Created if not already done
        if ($this->current(true)) {
            $this->commit();
        }
        //====================================================================//
        // Create a New Object Field
        $this->current = new ObjectField($fieldType, $fieldId);
        //====================================================================//
        // Set Field Name
        if ($fieldName) {
            $this->current->setName($fieldName);
        }

        return $this;
    }

    //====================================================================//
    //  FIELDS - LIST MANAGEMENT
    //====================================================================//

    /**
     * Save Current Edited Field & Apply Configurators
     */
    public function build(): self
    {
        //====================================================================//
        // Commit Last Created if not already done
        $this->commit();
        //====================================================================//
        // Safety Checks
        if (empty($this->getCollection()->count())) {
            return $this;
        }
        //====================================================================//
        // Execute Configurators on Fields
        $this->executeConfigurators();

        return $this;
    }

    /**
     * Save Current New Field in list & Clean current new field
     *
     * @return null|array<string, array>
     *
     * @phpstan-return  null|array<string, FIELD>
     */
    public function publish(): ?array
    {
        //====================================================================//
        // Finalize / Build Collection
        $this->build();
        //====================================================================//
        // Safety Checks
        if (empty($this->getCollection()->count())) {
            return Splash::log()->errNull("ErrFieldsNoList");
        }
        //====================================================================//
        // Extract Fields as Array
        $fields = $this->getCollection()->toArray();
        //====================================================================//
        // Reset Fields Factory
        $this->reset();

        return $fields;
    }

    //====================================================================//
    //  FIELDS - PRIVATE METHODS
    //====================================================================//

    /**
     * Validate & Push Current Edited Field to Collection
     */
    protected function commit(): self
    {
        //====================================================================//
        // Safety Checks
        if (!$current = $this->current(true)) {
            return $this;
        }
        //====================================================================//
        // Validate & Insert Current Field to Collection
        if ($current->isValid()) {
            $this->getCollection()->add($current);
        }
        $this->current = null;

        return $this;
    }

    /**
     * Reset Field Factory
     */
    private function reset(): void
    {
        $this->resetCollection();
        $this->dfLanguage = null;
        $this->configurators = array();
    }
}
