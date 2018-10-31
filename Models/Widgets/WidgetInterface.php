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
 *  @copyright 2015-2019 Splash Sync
 *  @license   GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007
 *
 **/

namespace   Splash\Models\Widget;

/**
 * @abstract    Splash Widget Interface
 */
interface WidgetInterface
{

    /**
     *  @abstract   Get Description Array for requested Widget Type
     *
     *  @return     array
     */
    public function description();
            
    /**
     * @abstract    Return requested Widget Data
     *
     * @param       string  $WidgetId             Widget Id.
     * @param       array   $Parameters           List of Parameters
     *
     * @return      array                   Widget Data
    */
    public function get($WidgetId = null, $Parameters = array());

}
