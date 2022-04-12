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

namespace   Splash\Models\Widgets;

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
    public function description(): array;

    /**
     * Return requested Widget Data
     *
     * @param array $parameters List of Parameters
     *
     * @return null|array Widget Data
     */
    public function get(array $parameters = array()): ?array;

    /**
     * Return Widget Status
     *
     * @return bool
     */
    public static function isDisabled(): bool;
}
