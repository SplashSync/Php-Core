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
class SplDefinition
{
    /**
     * Version of the Splash Module
     */
    public const VERSION = '3.0.0';

    /**
     * Version of the Splash Communication Protocol
     */
    public const PROTOCOL = '1.2';

    /**
     * Minimal PHP Versions
     */
    public const MIN_PHP_VERSION = "7.4.0";

    /**
     * Required PHP Extensions
     */
    public const MIN_PHP_EXT = array('xml', 'soap', 'curl', 'json', 'iconv');

    /**
     * Name of the Splash Module
     */
    public const NAME = 'Splash Php Client Module';

    /**
     * Description of the Splash Module
     */
    public const DESC = 'Splash Open-Source & Universal Synchronisation WebService.';

    /**
     * Author of the Splash Module
     */
    public const AUTHOR = 'Splash Official <www.splashsync.com>';

    /**
     * Default Url for Splash Sync Server
     *
     * @var string
     */
    const HOST = 'proxy.splashsync.com/ws/soap';

    /**
     * Module Webservice Method
     */
    public const WS_METHOD = "SOAP";

    /**
     * Client response timeout in seconds
     */
    public const TIMEOUT = 30;

    /**
     * Data Encryption Method
     */
    public const CRYPT_METHOD = "AES-256-CBC";

    /**
     * Messages Encoding Format (XML, JSON)
     */
    public const ENCODE = "XML";

    /**
     * Prefix to be applied to all local classes
     */
    public const CLASS_PREFIX = "\\Splash\\Local";

    /**
     * Prefix to be applied to all local objects classes
     */
    public const OBJECTS_PREFIX = "\\Splash\\Local\\Objects\\";

    /**
     * Prefix to be applied to all local widgets classes
     */
    public const WIDGETS_PREFIX = "\\Splash\\Local\\Widgets\\";

    /**
     * Module Default Translation Langage
     */
    public const DF_LANG = "en_US";

    /**
     * Enable activity logging on INI file
     */
    public const LOGGING = false;

    /**
     * Log Inputs Messages
     */
    public const TRACE_IN = false;

    /**
     * Log Outputs Messages
     */
    public const TRACE_OUT = false;

    /**
     * Log Tasks Execution Events
     */
    public const TRACE_TASKS = false;

    /**
     * Smart Notifications => Only warning & errors on commit events
     */
    public const SMART_NOTIFY = false;
}
