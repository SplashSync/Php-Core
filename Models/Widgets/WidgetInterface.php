<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2019 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace   Splash\Models\Widgets;

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
     * @param       array   $parameters           List of Parameters
     *
     * @return      array|false                   Widget Data
     */
    public function get($parameters = array());
    
    /**
     * @abstract   Return Widget Status
     */
    public static function getIsDisabled();
}
