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

namespace   Splash\Models\Widgets;

use ArrayObject;

/**
 * Splash Widget Interface
 */
interface WidgetInterface
{
    /**
     * Get Description Array for requested Widget Type
     *
     * @return array
     */
    public function description();

    /**
     * Return requested Widget Data
     *
     * @param array|ArrayObject $parameters List of Parameters
     *
     * @return array|false Widget Data
     */
    public function get($parameters = array());

    /**
     * Return Widget Status
     *
     * @return null|bool
     */
    public static function getIsDisabled();
}
