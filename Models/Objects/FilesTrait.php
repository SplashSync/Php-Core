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

use Splash\Models\Helpers\FilesHelper;

/**
 * This class implements access to Files Fields Helper.
 */
trait FilesTrait
{
    /**
     * Static Class Storage
     *
     * @var null|FilesHelper
     */
    private static ?FilesHelper $filesHelper = null;

    /**
     * Get a singleton List Helper Class
     *
     * @return FilesHelper
     */
    public static function files(): FilesHelper
    {
        //====================================================================//
        // Initialize Class
        if (!isset(self::$filesHelper)) {
            self::$filesHelper = new FilesHelper();
        }
        //====================================================================//
        // Return Helper Class
        return self::$filesHelper;
    }
}
