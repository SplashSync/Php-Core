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

use Splash\Models\Helpers\ObjectsHelper;

/**
 * @abstract    This class implements access to Objects Links Fields Helper.
 */
trait ObjectsTrait
{
    /**
     * @var ObjectsHelper
     */
    private static $ObjectsHelper;

    /**
     *      @abstract   Get a singleton Objects Helper Class
     *
     *      @return     ObjectsHelper
     */
    public static function objects()
    {
        // Helper Class Exists
        if (isset(self::$ObjectsHelper)) {
            return self::$ObjectsHelper;
        }
        // Initialize Class
        self::$ObjectsHelper = new ObjectsHelper();
        // Return Helper Class
        return self::$ObjectsHelper;
    }
}
