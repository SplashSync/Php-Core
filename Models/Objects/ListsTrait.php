<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
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
 * @abstract    This class implements access to List Fields Helper.
 */
trait ListsTrait
{
    /**
     * @var ListsHelper
     */
    private static $ListsHelper;

    /**
     *      @abstract   Get a singleton List Helper Class
     *
     *      @return     ListsHelper
     */
    public static function lists()
    {
        // Helper Class Exists
        if (isset(self::$ListsHelper)) {
            return self::$ListsHelper;
        }
        // Initialize Class
        self::$ListsHelper = new ListsHelper();
        // Return Helper Class
        return self::$ListsHelper;
    }
}
