<?php
/**
 * This file is part of SplashSync Project.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 *  @author    Splash Sync <www.splashsync.com>
 *  @copyright 2015-2017 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 *
 **/

namespace   Splash\Models\Objects;

use Splash\Models\Helpers\PricesHelper;

/**
 * @abstract    This class implements access to Prices Fields Helper.
 */
trait PricesTrait
{
    /**
     * @var Static Class Storage
     */
    private static $PricesHelper;
    
    /**
     *      @abstract   Get a singleton Prices Helper Class
     *
     *      @return     PricesHelper
     */
    public static function prices()
    {
        // Helper Class Exists
        if (isset(self::$PricesHelper)) {
            return self::$PricesHelper;
        }
        // Initialize Class
        self::$PricesHelper        = new PricesHelper();
        // Return Helper Class
        return self::$PricesHelper;
    }
}
