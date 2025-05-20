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

use Countable;
use Iterator;
use Splash\Core\Interfaces\Fields\Collection\FieldsActionsInterface;
use Splash\Core\Interfaces\Fields\Collection\FieldsFilteringInterface;
use Splash\Core\Interfaces\Fields\Collection\FieldsFinderInterface;
use Splash\Core\Interfaces\Fields\Collection\FieldsGroupsInterface;
use Splash\Core\Models\Fields\AbstractField;

/**
 * Common Interface to Fields Collections
 *
 * @template-extends Iterator<string, AbstractField>
 */
interface FieldsCollectionInterface extends
    Iterator,
    Countable,
    FieldsFilteringInterface,
    FieldsActionsInterface,
    FieldsFinderInterface,
    FieldsGroupsInterface
{
    /**
     * Get a Field from Collection
     */
    public function get(string $fieldId): ?AbstractField;

    /**
     * Check if a Field is Present on Collection
     */
    public function has(string $fieldId): bool;
}
