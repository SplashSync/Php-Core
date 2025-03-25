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

namespace Splash\Framework\Dictionary;

/**
 * Splash Client Available Services Names
 */
class SplServices
{
    /**
     * Connexion tests, only to check availability & access to remote server
     *
     * When using generic Splash Sync protocol, this request isn't encrypted
     */
    public const PING = "Ping";

    /**
     * Connect to remote client and read server information
     *
     *  When using generic Splash Sync protocol, this request is encrypted
     */
    public const CONNECT = "Connect";

    /**
     * Global Remote Client information retrieval service
     */
    public const ADMIN = "Admin";

    /**
     * Common Data Transactions Service
     * - Read Objects Fields
     * - Read / Write / Delete Objects Data
     */
    public const OBJECTS = "Objects";

    /**
     * Files exchanges service
     * - Check existence of a file
     * - Get file raw contents
     */
    public const FILE = "Files";

    /**
     * Information blocks retrieval service
     * - Read Widgets Definition & Configuration Fields
     * - Read Widgets Contents
     */
    public const WIDGETS = "Widgets";

    /**
     * List of All Available Services
     */
    public const ALL = array(
        self::PING,
        self::CONNECT,
        self::ADMIN,
        self::OBJECTS,
        self::FILE,
        self::WIDGETS,
    );
}
