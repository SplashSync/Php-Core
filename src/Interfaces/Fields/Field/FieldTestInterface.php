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
 * Interface for Splash Object Field Test Options
 */
interface FieldTestInterface
{
    /**
     * Add Associated Field. Fields to Generate with this field.
     */
    public function addAssociation(string $fieldId): self;

    /**
     * Set Associated Fields. Fields to Generate with this field.
     *
     * @param string[] $fieldIds
     */
    public function setAssociations(array $fieldIds): self;

    /**
     * Get Associated Fields. Fields to Generate with this field.
     *
     * @return string[]
     */
    public function getAssociations(): array;

    /**
     * Set Field Not Tested Flag
     * Do No Perform Set Tests for this Field
     */
    public function setNotTested(bool $noTests): self;

    /**
     * Get Field Not Tested Flag
     * Do No Perform Set Tests for this Field
     */
    public function isNotTested(): bool;
}
