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

namespace Splash\Core\Dictionary;

/**
 * Dictionary for a WebService Object Actions / Operations
 */
class SplOperations
{
    /**
     * Object locally created
     */
    public const CREATE = 'create';

    /**
     * Object was locally modified
     */
    public const UPDATE = 'update';

    /**
     * Object locally deleted
     */
    public const DELETE = 'delete';

    /**
     * Object Identifier Was Modified
     */
    public const RENAME = 'rename';
}
