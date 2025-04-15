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

/**
 * Interface for Splash Object Field Core Definition
 */
interface FieldCoreInterface
{
    /**
     * Set Field Identifier
     */
    public function setIdentifier(string $id): self;

    /**
     * Get Field Identifier
     */
    public function getIdentifier(): string;

    /**
     * Get Field Type
     */
    public function getType(): string;

    /**
     * Set Field Name
     */
    public function setName(string $name): self;

    /**
     * Get Field Name
     */
    public function getName(): string;

    /**
     * Set Field Description
     */
    public function setDesc(string $desc): self;

    /**
     * Get Field Description
     */
    public function getDesc(): ?string;

    /**
     * Set Field Group Name
     */
    public function setGroup(string $group): self;

    /**
     * Get Field Group Name
     */
    public function getGroup(): ?string;

    /**
     * Push Field Inside a List
     */
    public function setInlist(string $listName): self;

    /**
     * Check if Field is Inside a List
     */
    public function isInlist(): bool;

    /**
     * If in List => Field List Name
     */
    public function getListName(): ?string;

    /**
     * If in List => Field Final Field Name
     */
    public function getListFieldName(): ?string;

    /**
     * If in List => Field Final Field Type
     */
    public function getListFieldType(): ?string;
}
