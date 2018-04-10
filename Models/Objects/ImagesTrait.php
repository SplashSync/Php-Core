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

use Splash\Models\Helpers\ImagesHelper;

/**
 * @abstract    This class implements access to Images Fields Helper.
 */
trait ImagesTrait
{
    /**
     * @var Static Class Storage
     */
    private static $ImagesHelper;
    
    /**
     *      @abstract   Get a singleton List Helper Class
     *
     *      @return     ImagesHelper
     */
    public static function images()
    {
        // Helper Class Exists
        if (isset(self::$ImagesHelper)) {
            return self::$ImagesHelper;
        }
        // Initialize Class
        self::$ImagesHelper        = new ImagesHelper();
        // Return Helper Class
        return self::$ImagesHelper;
    }
}
