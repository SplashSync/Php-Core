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

namespace Splash\Core\Interfaces\Fields\Collection;

use Splash\Core\Interfaces\Fields\FieldInterface;
use Splash\Core\Models\Fields\AbstractField;

/**
 * Execute Actions on Collection Fields
 *
 * @phpstan-import-type FIELD from FieldInterface
 */
interface FieldsActionsInterface
{
    /**
     * Convert Fields Collection to Array of Fields Definition
     *
     * @phpstan-return array<string, FIELD>
     *
     * @return array<string, array>
     */
    public function toArray(): array;

    /**
     * Get reduced collection => List of Fields IDs
     *
     * @return string[]
     */
    public function reduce(): array;

    /**
     * Filter on Unique Field that match Request Criteria
     */
    public function unique(): ?AbstractField;
}
