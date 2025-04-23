<?php

namespace Splash\Core\Interfaces\Fields;

interface FieldConstraintInterface
{
    /**
     * Create a new Field Constraint
     */
    public function __construct(string $itemType, string $itemProp);

    /**
     * Create a new Field Constraint from a Field Template
     */
    public function fromTemplate(FieldTemplateInterface $template): FieldConstraintInterface;

    /**
     * Target Field Item Type
     */
    public function getItemType(): string;

    /**
     * Target Field Item Prop
     */
    public function getItemProp(): string;

    /**
     * Mark Field as Optional
     */
    public function isOptional(): bool;

    /**
     * Mark Field as Optional
     */
    public function setOptional(bool $optional = true): self;

    /**
     * Mark Field Constraint as Improvement
     *
     */
    public function isImprovement(): bool;

    /**
     * Mark Field as Improvement
     */
    public function setImprovement(bool $improvement = true): self;

    /**
     * Shall this field be required?
     *
     * @return null|bool Skipp test if null
     */
    public function isRequired(): ?bool;

    /**
     * Shall this field be required?
     */
    public function setRequired(?bool $required = true): self;

    /**
     * Shall this field be read?
     *
     * @return null|bool Skipp test if null
     */
    public function isRead(): ?bool;

    /**
     * Shall this field be read?
     */
    public function setRead(?bool $read = true): self;

    /**
     * Shall this field be written?
     *
     * @return null|bool Skipp test if null
     */
    public function isWrite(): ?bool;


    /**
     * Shall this field be written?
     */
    public function setWrite(?bool $write = true): self;

    /**
     * Shall this field be primary?
     *
     * @return null|bool Skipp test if null
     */
    public function isPrimary(): ?bool;

    /**
     * Shall this field be primary?
     */
    public function setPrimary(?bool $primary = true): self;

    /**
     * Shall this field be indexed?
     *
     * @return null|bool Skipp test if null
     */
    public function isIndexed(): ?bool;

    /**
     * Shall this field be indexed?
     */
    public function setIndexed(?bool $indexed = true): self;

    /**
     * Shall this field be logged?
     *
     * @return null|bool Skipp test if null
     */
    public function isLogged(): ?bool;

    /**
     * Shall this field be logged?
     */
    public function setLogged(?bool $logged = true): self;

    /**
     * Shall this field be in a specified format?
     *
     * @return null|string Skipp test if null
     */
    public function isFormat(): ?string;

    /**
     * Shall this field be in a specified format?
     */
    public function setFormat(?string $format): self;
}