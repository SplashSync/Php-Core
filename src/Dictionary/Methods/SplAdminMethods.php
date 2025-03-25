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
 * List of Available Webservice Client Methods for Administration Service
 */
class SplAdminMethods
{
    /**
     * Get Server Information (Name, Address and more...)
     */
    public const INFOS = 'infos';

    /**
     * Get List of Available Objects for this Client
     */
    public const OBJECTS = 'objects';

    /**
     * Get Result of SelfTest Sequence
     */
    public const SELF_TEST = 'selftest';

    /**
     * Get List of Available Widgets for this Client
     */
    public const WIDGETS = 'widgets';

    /**
     * List of All Available Methods
     */
    public const ALL = array(
        self::INFOS,
        self::OBJECTS,
        self::SELF_TEST,
        self::WIDGETS,
    );
}
