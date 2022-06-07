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

use Splash\Components\UnitConverter;

/**
 * Helper for Units Conversion
 */
trait UnitsHelperTrait
{
    /**
     * @var null|UnitConverter
     */
    private static ?UnitConverter $unitConverter = null;

    /**
     * Get a singleton Unit Converter Class
     *
     * @return UnitConverter
     */
    public static function units(): UnitConverter
    {
        //====================================================================//
        // Initialize Class
        if (!isset(self::$unitConverter)) {
            self::$unitConverter = new UnitConverter();
        }
        //====================================================================//
        // Return Helper Class
        return self::$unitConverter;
    }
}
