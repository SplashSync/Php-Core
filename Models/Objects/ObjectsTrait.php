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

use Splash\Models\Helpers\ObjectsHelper;

/**
 * This class implements access to Object Links Fields Helper.
 */
trait ObjectsTrait
{
    /**
     * @var null|ObjectsHelper
     */
    private static ?ObjectsHelper $objectsHelper = null;

    /**
     * Get a singleton Objects Helper Class
     *
     * @return ObjectsHelper
     */
    public static function objects(): ObjectsHelper
    {
        //====================================================================//
        // Initialize Class
        if (!isset(self::$objectsHelper)) {
            self::$objectsHelper = new ObjectsHelper();
        }
        //====================================================================//
        // Return Helper Class
        return self::$objectsHelper;
    }
}
