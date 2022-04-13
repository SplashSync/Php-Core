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

namespace Splash\Local\Objects\Filters;

use Splash\Client\Splash;
use Splash\Models\ObjectFilterInterface;

/**
 * TEMPLATE - Dummy Objects Filter
 *
 * Use Splash Objects Filters to prevent Changes Commit, Data Reading && Writing
 *  - Place it on one of your app extension folder
 *  - Preserve class namespace
 *
 * In this exemple, we fileter "Dummy" Objects where "my_custom_field" value is "filtered"
 */
class DummyFilter implements ObjectFilterInterface
{
    /**
     * {@inheritDoc}
     */
    public function getFilteredTypes(): array
    {
        return array('Dummy');
    }

    /**
     * {@inheritDoc}
     */
    public function isFiltered(string $objectType, string $objectId, ?object $object): bool
    {
        //====================================================================//
        // Load Object if Needed
        /** @phpstan-ignore-next-line */
        $object = $object ?? $object::load($objectId);
        //====================================================================//
        // Verify Object Values
        if ("filtered" == $object->my_custom_field) {
            return true;
        }

        return false;
    }
}
