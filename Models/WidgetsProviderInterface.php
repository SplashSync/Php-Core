<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Models;

use Splash\Models\Widgets\WidgetInterface;

/**
 * @abstract    Local Objects Provider Interface.
 *              Used to Override Core Objects Mapper (by Files)
 */
interface WidgetsProviderInterface
{
    /**
     * @abstract   Build list of Available Widgets
     *
     * @return string[]
     */
    public function widgets();

    /**
     * @abstract   Get Specific Widgets Class
     *             This function is a router for all local Widgets classes & functions
     *
     * @params     string $WidgetType       Specify Widgets Type Name
     *
     * @return WidgetInterface
     */
    public function widget(string $widgetType);
}
