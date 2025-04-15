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
use Splash\Core\Helpers\StringConverter;
use Splash\Core\Interfaces\Fields\FieldInterface;

/**
 * Splash Object Field Options
 *
 * @phpstan-import-type RAW_CHOICE from FieldInterface
 */
trait FieldOptionsTrait
{
    /**
     * Field Possible Values
     *
     * @var array<int|string, RAW_CHOICE>
     */
    private array $choices = array();

    /**
     * Field Options
     *
     * @var array<string, scalar>
     */
    private array $options = array();

    /**
     * @inheritdoc
     */
    public function setMultiLang(?string $isoCode, bool $isDefault): static
    {
        //====================================================================//
        // Safety Checks ==> Verify Language ISO Code
        if (!self::isValidIsoCode((string) $isoCode)) {
            return $this;
        }
        //====================================================================//
        // Safety Checks ==> Verify Field Type is Allowed
        $fieldType = $this->getListFieldType() ?? $this->getType();
        if (!self::isValidMultiLangType($fieldType)) {
            Splash::log()->err("ErrFieldsWrongLang");
            Splash::log()->err("Received: ".$fieldType);

            return $this;
        }
        //====================================================================//
        // Default Language ==> Only Setup Language Option
        $this->addOption("language", (string) $isoCode);
        //====================================================================//
        // Other Language ==> Complete Field Setup
        if (!$isDefault) {
            $this->setIdentifier($this->getIdentifier()."_".$isoCode);
            if (!empty($this->getItemType()) && !empty($this->getItemProp())) {
                $this->setMicroData(
                    $this->getItemType()."/".$isoCode,
                    $this->getItemProp()
                );
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setChoices(array $choices): static
    {
        foreach ($choices as $value => $description) {
            $this->addChoice(
                (string) $value,
                (string) ($description ?? ucfirst($value) ?: ucfirst($value))
            );
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addChoice(string $value, string $description): static
    {
        $this->choices[] = array(
            "key" => $value,
            "value" => (string) StringConverter::toUtf8($description)
        );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addOption(string $key, $value = true): static
    {
        //====================================================================//
        // Safety Checks ==> Verify Key
        if (empty($key)) {
            Splash::log()->err("Field Option Type Cannot be Empty");
        } else {
            //====================================================================//
            // Update New Field structure
            $this->options[$key] = $value;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRawChoices(): array
    {
        return $this->choices;
    }

    /**
     * @inheritDoc
     */
    public function getChoices(): array
    {
        $choices = array();

        foreach ($this->choices as $choice) {
            $choices[$choice["key"]] = $choice["value"];
        }

        return $choices;
    }

    /**
     * @inheritDoc
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Import / Override Field Options Definition
     *
     * @param array<string, mixed> $values Custom Values to Write
     */
    protected function updateOptionsValues(array $values): static
    {
        //==============================================================================
        // Import Field Options
        $options = $values[Props::OPTIONS] ?? null;
        if (is_iterable($options)) {
            foreach ($options as $key => $value) {
                if ($key && $value && is_string($key) && is_scalar($value)) {
                    $this->addOption($key, $value);
                }
            }
        }

        return $this;
    }

    /**
     * Import / Override Field Options Definition
     *
     * @param array<string, mixed> $values Custom Values to Write
     *
     * @SuppressWarnings(CyclomaticComplexity)
     */
    protected function updateChoicesValues(array $values): static
    {
        //==============================================================================
        // Import Field Choices
        $choices = $values[Props::CHOICES] ?? null;
        if (!is_iterable($choices)) {
            return $this;
        }
        $this->setChoices(array());
        foreach ($choices as $key => $choiceValue) {
            //==============================================================================
            // Raw Choices Received
            if ($key && $choiceValue && is_string($key) && is_string($choiceValue)) {
                $this->addChoice($key, $choiceValue);
            }
            //==============================================================================
            // Structured Choices Received
            if (is_array($choiceValue)) {
                $structKey = $choiceValue["key"] ?? null;
                $structValue = $choiceValue["value"] ?? null;
                if ($structKey && $structValue && is_string($structKey) && is_string($structValue)) {
                    $this->addChoice($structKey, $structValue);
                }
            }
        }

        return $this;
    }
}
