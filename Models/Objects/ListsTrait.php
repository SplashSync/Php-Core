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

namespace   Splash\Models\Objects;

use Splash\Models\Helpers\ListsHelper;

/**
 * This class implements access to List Fields Helper.
 */
trait ListsTrait
{
    /**
     * @var null|ListsHelper
     */
    private static ?ListsHelper $listsHelper = null;

    /**
     * Get a singleton List Helper Class
     *
     * @return ListsHelper
     */
    public static function lists(): ListsHelper
    {
        //====================================================================//
        // Initialize Class
        if (!isset(self::$listsHelper)) {
            self::$listsHelper = new ListsHelper();
        }
        //====================================================================//
        // Return Helper Class
        return self::$listsHelper;
    }
}
