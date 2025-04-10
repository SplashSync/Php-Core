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

use DateTime;

class DatesHelper
{
    /**
     * Date / Day Only Timestamps Format for Splash
     */
    const DATE_CAST = 'Y-m-d';

    /**
     * Datetime Day + Hour Timestamps Format for Splash
     */
    const DATETIME_CAST = 'Y-m-d H:i:s';

    /**
     * Convert DateTime to formated Splash Date String
     */
    public static function toDateStr(DateTime $date, string $format = null): string
    {
        $format ??= self::DATE_CAST;

        return $date->format($format);
    }

    /**
     * Convert DateTime to formated Splash DateTime String
     */
    public static function toDateTimeStr(DateTime $date): string
    {
        return $date->format(self::DATETIME_CAST);
    }
}
