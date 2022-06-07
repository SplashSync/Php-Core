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

use Splash\Models\Helpers\ImagesHelper;

/**
 * This class implements access to Image Fields Helper.
 */
trait ImagesTrait
{
    /**
     * Static Class Storage
     *
     * @var null|ImagesHelper
     */
    private static ?ImagesHelper $imagesHelper = null;

    /**
     * Get a singleton List Helper Class
     *
     * @return ImagesHelper
     */
    public static function images(): ImagesHelper
    {
        //====================================================================//
        // Initialize Class
        if (!isset(self::$imagesHelper)) {
            self::$imagesHelper = new ImagesHelper();
        }
        //====================================================================//
        // Return Helper Class
        return self::$imagesHelper;
    }
}
