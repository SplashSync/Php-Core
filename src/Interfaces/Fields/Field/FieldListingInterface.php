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
 * Interface for Splash Object Field Listing Configuration
 */
interface FieldListingInterface
{
    /**
     * Set Field is In Object List Flag
     * This field is available in Objects List.
     */
    public function setListed(bool $listed): self;

    /**
     * Get Field is In Object List Flag
     * This field is available in Objects List.
     */
    public function isListed(): bool;

    /**
     * Set Field In Hidden Object List Flag
     *
     * This field is in Objects List but Hidden.
     * This improves reading of lists, but makes field usable for analyzes.
     */
    public function setListHidden(bool $listHidden): self;

    /**
     * Get Field In Hidden Object List Flag
     */
    public function isListHidden(): bool;
}
