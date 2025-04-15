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
 * Splash Object Field Listing Configuration
 */
trait FieldListingTrait
{
    /**
     * Field Required Flag
     */
    private bool $listed = false;

    /**
     * Field In Hidden Object List Flag
     */
    private bool $listHidden = false;

    /**
     * @inheritDoc
     */
    public function setListed(bool $listed): static
    {
        $this->listed = $listed;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isListed(): bool
    {
        return $this->listed;
    }

    /**
     * @inheritDoc
     */
    public function setListHidden(bool $listHidden): static
    {
        $this->listHidden = $listHidden;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isListHidden(): bool
    {
        return $this->listHidden;
    }

    //==============================================================================
    // Data Imports Management
    //==============================================================================

    /**
     * Import / Override Field Listing Definition
     *
     * @param array<string, mixed> $values Custom Values to Write
     */
    protected function updateListingValues(array $values): static
    {
        $this->updateBooleanValue($values, Props::IN_LIST, fn ($value) => $this->setListed($value));
        $this->updateBooleanValue($values, Props::HIDDEN_IN_LIST, fn ($value) => $this->setListHidden($value));

        return $this;
    }
}
