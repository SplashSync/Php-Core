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
 * List of Available Webservice Client Methods for Objects Service
 */
class SplObjectMethods
{
    /**
     * Get List of Available Objects
     */
    public const OBJECTS = 'Objects';

    /**
     * Read Object Description
     */
    public const DESC = 'Description';

    /**
     * Read Object Available Fields List
     */
    public const FIELDS = 'Fields';

    /**
     * Read Object List
     */
    public const LIST = 'ObjectsList';

    /**
     * Identify Object by Primary Keys
     */
    public const IDENTIFY = 'Identify';

    /**
     * Read Object Data
     */
    public const GET = 'Get';

    /**
     * Write Object Data
     */
    public const SET = 'Set';

    /**
     * Delete An Object
     */
    public const DEL = 'Delete';

    /**
     * List of All Available Methods
     */
    public const ALL = array(
        self::OBJECTS,
        self::DESC,
        self::FIELDS,
        self::LIST,
        self::IDENTIFY,
        self::GET,
        self::SET,
        self::DEL,
    );
}
