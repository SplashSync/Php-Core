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

namespace Splash\Core\Dictionary\Fields;

/**
 * Dictionary for All Splash Fields Properties Names
 */
class SplFieldProps
{
    /**
     * Field Object Unique Identifier
     */
    public const ID = "id";

    /**
     * Field Format Type Name
     */
    public const TYPE = "type";

    /**
     * Field Humanized Name (String)
     */
    public const NAME = "name";

    /**
     * Field Description (String)
     */
    public const DESC = "desc";

    /**
     * Field Section/Group (String)
     */
    public const GROUP = "group";

    /**
     * Field is Required to Create a New Object (Bool)
     */
    public const REQUIRED = "required";

    /**
     * Field is Readable (Bool)
     */
    public const READ = "read";

    /**
     * Field is Writable (Bool)
     */
    public const WRITE = "write";

    /**
     * Field Should be Indexed for Text Search (Bool)
     */
    public const INDEX = "index";

    /**
     * Field is Available in Object List Response (Bool)
     */
    public const IN_LIST = "inlist";

    /**
     * Field is Available in Object List but Hidden (Bool)
     */
    public const HIDDEN_IN_LIST = "hlist";

    /**
     * Field is a Primary Key (Bool)
     */
    public const PRIMARY = "primary";

    /**
     * Field Favorite Sync Mode (read|write|both)
     */
    public const SYNC_MODE = "syncmode";

    /**
     * Field Unique Schema.Org Object Url
     */
    public const MICRODATA_URL = "itemtype";

    /**
     * Field Unique Schema.Org "Like" Property Name
     */
    public const MICRODATA_PROP = "itemprop";

    /**
     * Field Unique Linker Tags (Self-Generated)
     */
    public const TAG = "tag";

    /**
     * Possible Values used in Editor & Debugger Only  (Array)
     */
    public const CHOICES = "choices";

    /**
     * Field is To Log (Bool)
     */
    public const LOG = "log";

    /**
     * Associated Fields. Fields to Generate with this field.
     */
    public const ASSO = "asso";

    /**
     * Fields Constraints to Generate Fake Data during Tests
     */
    public const OPTIONS = "options";

    /**
     * Do No Perform Tests for this Field
     */
    public const NO_TEST = "notest";
}
