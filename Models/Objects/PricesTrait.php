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

use Splash\Models\Helpers\PricesHelper;

/**
 * This class implements access to Price Fields Helper.
 */
trait PricesTrait
{
    /**
     * @var null|PricesHelper
     */
    private static ?PricesHelper $pricesHelper = null;

    /**
     * Get a singleton Prices Helper Class
     *
     * @return PricesHelper
     */
    public static function prices(): PricesHelper
    {
        //====================================================================//
        // Initialize Class
        if (!isset(self::$pricesHelper)) {
            self::$pricesHelper = new PricesHelper();
        }
        //====================================================================//
        // Return Helper Class
        return self::$pricesHelper;
    }
}
