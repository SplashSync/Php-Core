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
 * List of Available Webservice Client Methods for Files Service
 */
class SplFilesMethods
{
    /**
     * Check if file exist on Remote Client
     */
    public const EXISTS = 'isFile';

    /**
     * Download file from Remote Client
     */
    public const GET = 'ReadFile';

    /**
     * Upload file to Remote Client
     */
    public const SET = 'SetFile';

    /**
     * Delete file on Remote Client
     */
    public const DEL = 'DeleteFile';

    /**
     * List of All Available Methods
     */
    public const ALL = array(
        self::EXISTS,
        self::GET,
        self::SET,
        self::DEL,
    );
}
