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

namespace Splash\Framework\Dictionary\Methods;

/**
 * List of Available Webservice Client Methods for Widgets Service
 */
class SplWidgetsMethods
{
    /**
     * Get List of Available Widgets
     */
    public const LIST = 'WidgetsList';

    /**
     * Get Widget Definition
     */
    public const DEFINITION = 'Description';

    /**
     * Get Information
     */
    public const GET = 'Get';

    /**
     * List of All Available Methods
     */
    public const ALL = array(
        self::LIST,
        self::DEFINITION,
        self::GET,
    );
}
