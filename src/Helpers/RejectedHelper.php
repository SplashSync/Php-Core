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

namespace Splash\Core\Helpers;

/**
 * Manage Rejected Objects by Using a Dedicated ID Format
 */
class RejectedHelper
{
    /**
     * Rejected Object ID Prefix
     */
    private const PREFIX = 'rejected';

    /**
     * Check if Object ID is a Rejected ID
     */
    public static function isRejected(?string $objectId): bool
    {
        return !empty($objectId)
            && str_starts_with(strtolower($objectId), self::PREFIX)
        ;
    }

    /**
     * Build a Rejected ID
     */
    public static function toRejected(?string $suffix = null): string
    {
        return sprintf("%s%s", strtoupper(self::PREFIX), $suffix);
    }
}
