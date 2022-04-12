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

namespace Splash\Models;

interface ObjectFilterInterface
{
    /**
     * Get list of Splash Object Types Filtered by this Class
     *
     * @return string[]
     */
    public function getFilteredTypes(): array;

    /**
     * Check if this Object is Filtered.
     *
     * If possible, Object is provided, but if not, only Object ID
     *
     * @param string      $objectType Object Type Name
     * @param string      $objectId   Object ID
     * @param null|object $object     Object (Optional)
     *
     * @return bool
     */
    public function isFiltered(string $objectType, string $objectId, ?object $object): bool;
}
