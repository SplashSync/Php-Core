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

use Splash\Core\Dictionary\Fields\SplFieldProps as Props;

/**
 * Interface for Splash Object Field Test Options
 */
trait FieldTestTrait
{
    /**
     * Associated Field. Fields to Generate with this field.
     *
     * @var string[]
     */
    private array $associations = array();

    /**
     * Do No Perform Tests for this Field
     */
    private bool $noTests = false;

    /**
     * @inheritDoc
     */
    public function addAssociation(string $fieldId): static
    {
        $this->associations[] = $fieldId;
        $this->associations = array_unique($this->associations);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setAssociations(array $fieldIds): static
    {
        $this->associations = array_unique($fieldIds);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAssociations(): array
    {
        return $this->associations;
    }

    /**
     * @inheritDoc
     */
    public function setNotTested(bool $noTests): static
    {
        $this->noTests = $noTests;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isNotTested(): bool
    {
        return $this->noTests;
    }

    /**
     * Import / Override Field Testing Definition
     *
     * @param array<string, mixed> $values Custom Values to Write
     */
    protected function updateTestValues(array $values): static
    {
        //==============================================================================
        // Import Associated Fields
        $associations = $values[Props::ASSO] ?? null;
        if (is_iterable($associations)) {
            $this->setAssociations(array());
            foreach ($associations as $value) {
                if ($value && is_string($value)) {
                    $this->addAssociation($value);
                }
            }
        }
        //==============================================================================
        // Import No Tests Flag
        $this->updateBooleanValue($values, Props::NO_TEST, fn ($value) => $this->setNotTested($value));

        return $this;
    }
}
