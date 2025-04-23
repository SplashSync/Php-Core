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

namespace Splash\Core\Dictionary\Objects;

/**
 * Dictionary for All Splash Objects Properties Names
 */
class SplObjectProps
{
    /**
     * Object Type Name
     * This is the main type for SPlash Objects, used as code anywhere
     */
    public const TYPE = "type";

    /**
     * Object Humanized Name (String)
     * -> Make it Short, it will be displayed everywhere
     */
    public const NAME = "name";

    /**
     * Object Description (String)
     */
    public const DESC = "description";

    /**
     * Object Fontawesome Icon
     */
    public const ICON = "icon";

    /**
     * Object Disable Flag (bool)
     */
    public const DISABLED = "disabled";

    //====================================================================//
    // Object Limitations
    // Flags used by Splash Server to Prevent Unexpected Operations on Remote Server
    //====================================================================//

    /**
     * Allow Creation Of New Local Objects
     */
    public const ALLOW_CREATE = "allow_push_created";

    /**
     * Allow Update Of Existing Local Objects
     */
    public const ALLOW_UPDATE = "allow_push_updated";

    /**
     * Allow To Delete an Existing Local Objects
     */
    public const ALLOW_DELETE = "allow_push_deleted";

    //====================================================================//
    // Object Default Configuration
    // Flags used by Splash Server to generate Default Objects Configuration
    //====================================================================//

    /**
     * Enable Creation Of New Local Objects when Not Existing
     */
    public const PUSH_CREATED = "enable_push_created";

    /**
     * Enable Update Of Existing Local Objects when Modified Remotely
     */
    public const PUSH_UPDATED = "enable_push_updated";

    /**
     * Enable Delete Of Existing Local Objects when Deleted Remotely
     */
    public const PUSH_DELETED = "enable_push_deleted";

    /**
     * Enable Import Of New Local Objects
     */
    public const PULL_CREATED = "enable_pull_created";

    /**
     * Enable Import of Updates of Local Objects when Modified Locally
     */
    public const PULL_UPDATED = "enable_pull_updated";

    /**
     * Enable Delete Of Remotes Objects when Deleted Locally
     */
    public const PULL_DELETED = "enable_pull_deleted";
}
