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
    public function fromTemplate(FieldTemplateInterface $template): FieldConstraintInterface
    {
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
