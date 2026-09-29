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

namespace Splash\Core\Interfaces\Fields;

interface FieldConstraintInterface
{
    /**
     * Create a new Field Constraint
     */
    public function __construct(string $itemType, string $itemProp);

    /**
     * Create a new Field Constraint from a Field Template Code or Class
     */
    public static function fromTemplateCode(
        string $templateCodeOrClass,
        ?string $isoLang = null
    ): FieldConstraintInterface;

    /**
     * Create a new Field Constraint from a Field Template
     */
    public static function fromTemplate(
        FieldTemplateInterface $template,
        ?string $isoLang = null
    ): FieldConstraintInterface;

    /**
     * Target Field Item Type
     */
    public function getItemType(): string;

    /**
     * Target Field Item Prop
     */
    public function getItemProp(): string;

    /**
     * Import an Array Configuration to a Template
     *
     * @param array<string, array|scalar> $configuration
     */
    public function configure(array $configuration): self;

    /**
     * Reset Field Constraint Configuration
     */
    public function reset(): self;

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

    /**
     * Get the Splash type the field is searched with, null to search on metadata only
     */
    public function getType(): ?string;

    /**
     * Restrict the search of the field to a Splash type
     *
     * A field is normally identified by its metadata only. Set a type ONLY
     * when several fields share the same metadata, so that the right one
     * is picked: i.e. "varchar" for the object level field, "varchar@list"
     * for the one inside a list.
     */
    public function setType(?string $type): self;

    /**
     * Add an alternative to this constraint: at least one of them must be satisfied
     *
     * The same data may be exposed under different fields, i.e. a product
     * identified by its link on an ERP or by its SKU on a WMS. The constraint
     * is satisfied as soon as one alternative is exposed with the right
     * flags, whatever the others. Optional applies to the group.
     */
    public function addAlternative(FieldConstraintInterface $alternative): self;

    /**
     * Get the alternatives of this constraint, empty when there are none
     *
     * @return FieldConstraintInterface[]
     */
    public function getAlternatives(): array;
}
