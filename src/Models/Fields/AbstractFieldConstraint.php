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

namespace Splash\Core\Models\Fields;

use Exception;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\Fields\SplFieldProps;
use Splash\Core\Helpers\FieldTemplatesHelper;
use Splash\Core\Interfaces\Fields\FieldConstraintInterface;
use Splash\Core\Interfaces\Fields\FieldTemplateInterface;

/**
 * Base Class for Implementing Fields Constraints
 */
abstract class AbstractFieldConstraint implements FieldConstraintInterface
{
    /**
     * Target Field Item type
     */
    private string $itemType;

    /**
     * Target Field Item type
     */
    private string $itemProp;

    /**
     * Mark Field as Optional
     */
    private bool $optional = false;

    /**
     * Mark Field as Improvement
     */
    private bool $improvement = false;

    /**
     * Shall this field be required?
     */
    private ?bool $required = null;

    /**
     * Shall this field be read?
     */
    private ?bool $read = null;

    /**
     * Shall this field be written?
     */
    private ?bool $write = null;

    /**
     * Shall this field be primary?
     */
    private ?bool $primary = null;

    /**
     * Shall this field be indexed?
     */
    private ?bool $indexed = null;

    /**
     * Shall this field be logged?
     */
    private ?bool $logged = null;

    /**
     * Shall this field be in a specified format?
     */
    private ?string $format = null;

    /**
     * @inheritDoc
     */
    public function __construct(string $itemType, string $itemProp)
    {
        $this->itemType = $itemType;
        $this->itemProp = $itemProp;
    }

    /**
     * @inheritDoc
     */
    public static function fromTemplateCode(
        string $templateCodeOrClass,
        ?string $isoLang = null
    ): FieldConstraintInterface {
        //====================================================================//
        // Safety Check - Template Exists
        if (!$fieldTemplate = FieldTemplatesHelper::resolve($templateCodeOrClass)) {
            Splash::log()->err(
                sprintf("Unable to detect Field Template %s: Wrong Code or Class", $templateCodeOrClass)
            );

            return (new static("Not Found", $templateCodeOrClass))->setRequired(true);
        }

        //====================================================================//
        // Configure Field Constraint from Template
        return self::fromTemplate($fieldTemplate, $isoLang);
    }

    /**
     * @inheritDoc
     */
    public static function fromTemplate(
        FieldTemplateInterface $template,
        ?string $isoLang = null
    ): FieldConstraintInterface {
        //====================================================================//
        // Get Field Template Configuration
        $configuration = $template->getConfiguration($isoLang);
        //====================================================================//
        // Safety Check - Template Define Microdata
        $itemType = $configuration[SplFieldProps::MICRODATA_URL] ?? null;
        $itemProp = $configuration[SplFieldProps::MICRODATA_PROP] ?? null;
        if (!$itemType || !$itemProp || !is_scalar($itemType) || !is_scalar($itemProp)) {
            Splash::log()->err(
                sprintf("Unable to create Field Constraint %s: No Microdata Defined", get_class($template))
            );

            return (new static("Invalid Template", $template->getName($isoLang)))->setRequired(true);
        }
        //====================================================================//
        // Create Field Constraint
        $fieldConstraint = new static((string) $itemType, (string) $itemProp);
        //====================================================================//
        // Default: Mark Field Constraint as Optional
        // This prevents throwing an Exception when Field is not mandatory
        $fieldConstraint->setOptional();

        //====================================================================//
        // Configure Field Constraint
        return $fieldConstraint->configure($configuration);
    }

    /**
     * @inerhitDoc
     *
     * @SuppressWarnings(CyclomaticComplexity)
     * @SuppressWarnings(NPathComplexity)
     */
    public function configure(array $configuration): self
    {
        //====================================================================//
        // Field Format Constraint
        if (!is_null($format = $configuration[SplFieldProps::TYPE] ?? null) && is_scalar($format)) {
            $this->setFormat((string) $format);
        }
        //====================================================================//
        // Field Flags Constraints
        if (!is_null($required = $configuration[SplFieldProps::REQUIRED] ?? null) && is_bool($required)) {
            $this->setRequired($required);
        }
        if (!is_null($primary = $configuration[SplFieldProps::PRIMARY] ?? null) && is_bool($primary)) {
            $this->setPrimary($primary);
        }
        if (!is_null($indexed = $configuration[SplFieldProps::INDEX] ?? null) && is_bool($indexed)) {
            $this->setIndexed($indexed);
        }
        if (!is_null($read = $configuration[SplFieldProps::READ] ?? null) && is_bool($read)) {
            $this->setRead($read);
        }
        if (!is_null($write = $configuration[SplFieldProps::WRITE] ?? null) && is_bool($write)) {
            $this->setWrite($write);
        }

        return $this;
    }

    /**
     * @inerhitDoc
     */
    public function reset(): self
    {
        $this->optional = false;
        $this->improvement = false;
        $this->required = null;
        $this->read = null;
        $this->write = null;
        $this->primary = null;
        $this->indexed = null;
        $this->logged = null;
        $this->format = null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getItemType(): string
    {
        return $this->itemType;
    }

    /**
     * @inheritDoc
     */
    public function getItemProp(): string
    {
        return $this->itemProp;
    }

    /**
     * @inheritDoc
     */
    public function isOptional(): bool
    {
        return $this->optional;
    }

    /**
     * @inheritDoc
     */
    public function setOptional(bool $optional = true): self
    {
        $this->optional = $optional;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isImprovement(): bool
    {
        return $this->improvement;
    }

    /**
     * @inheritDoc
     */
    public function setImprovement(bool $improvement = true): self
    {
        $this->improvement = $improvement;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * @inheritDoc
     */
    public function setRequired(?bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isRead(): ?bool
    {
        return $this->read;
    }

    /**
     * @inheritDoc
     */
    public function setRead(?bool $read = true): self
    {
        $this->read = $read;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isWrite(): ?bool
    {
        return $this->write;
    }

    /**
     * @inheritDoc
     */
    public function setWrite(?bool $write = true): self
    {
        $this->write = $write;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isPrimary(): ?bool
    {
        return $this->primary;
    }

    /**
     * @inheritDoc
     */
    public function setPrimary(?bool $primary = true): self
    {
        $this->primary = $primary;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isIndexed(): ?bool
    {
        return $this->indexed;
    }

    /**
     * @inheritDoc
     */
    public function setIndexed(?bool $indexed = true): self
    {
        $this->indexed = $indexed;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isLogged(): ?bool
    {
        return $this->logged;
    }

    /**
     * @inheritDoc
     */
    public function setLogged(?bool $logged = true): FieldConstraintInterface
    {
        $this->logged = $logged;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @inheritDoc
     */
    public function setFormat(?string $format): self
    {
        $this->format = $format;

        return $this;
    }
}
