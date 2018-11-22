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

use Splash\Models\Helpers\ListsHelper;

/**
 * @abstract    This class implements access to List Fields Helper.
 */
trait ListsTrait
{
    /**
     * @var ListsHelper
     */
    private static $ListsHelper;
    
    /**
     *      @abstract   Get a singleton List Helper Class
     *
     *      @return     ListsHelper
     */
    public static function lists()
    {
        // Helper Class Exists
        if (isset(self::$ListsHelper)) {
            return self::$ListsHelper;
        }
        // Initialize Class
        self::$ListsHelper        = new ListsHelper();
        // Return Helper Class
        return self::$ListsHelper;
    }
}
