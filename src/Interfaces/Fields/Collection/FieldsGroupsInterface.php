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

/**
 * Interface for Single Field Grouping Methods
 */
interface FieldsGroupsInterface
{
    /**
     * Get Collection Fields Groups
     *
     * @return array<string, string[]>
     */
    public function getGroups(): array;

    /**
     * Get Collection Fields Groups Names
     *
     * @return string[]
     */
    public function getGroupNames(): array;
}
