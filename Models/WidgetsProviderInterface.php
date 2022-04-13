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

namespace Splash\Models;

use Exception;
use Splash\Models\Widgets\WidgetInterface;

/**
 * Local Objects Provider Interface.
 * Used to Override Core Objects Mapper (by Files)
 */
interface WidgetsProviderInterface
{
    /**
     * Build list of Available Widgets
     *
     * @return string[]
     */
    public function widgets(): array;

    /**
     * Get Splash Widgets Class
     * This function is a router for all local Widgets classes & functions
     *
     * @param string $widgetType Specify Widgets Type Name
     *
     * @throws Exception
     *
     * @return WidgetInterface
     */
    public function widget(string $widgetType): WidgetInterface;
}
