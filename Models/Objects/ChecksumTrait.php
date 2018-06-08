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

use Splash\Models\Helpers\ChecksumHelper;

/**
 * @abstract    This class implements access to Checksum Fields Helper.
 */
trait ChecksumTrait
{
    /**
     * @var Static Class Storage
     */
    private static $ChecksumHelper;
    
    /**
     * @abstract   Get a singleton Checksum Helper Class
     * @return     ChecksumHelper
     */
    public static function md5()
    {
        // Helper Class Exists
        if (isset(self::$ChecksumHelper)) {
            return self::$ChecksumHelper;
        }
        // Initialize Class
        self::$ChecksumHelper        = new ChecksumHelper();
        // Return Helper Class
        return self::$ChecksumHelper;
    }
    
    /**
     * @abstract   Get a singleton Checksum Helper Class
     * @return     ChecksumHelper
     */
    public static function checksum()
    {
        return self::Md5();
    }
}
