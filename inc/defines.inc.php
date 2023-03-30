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

//====================================================================//
//====================================================================//
//  CONSTANTS DEFINITION
//====================================================================//
//====================================================================//

//====================================================================//
// Global Definitions
//====================================================================//
define("SPLASH_VERSION", '2.0.1');
define("SPLASH_NAME", 'Splash Php Client Module');
define("SPLASH_DESC", 'Splash Open-Source & Universal Synchronisation WebService.');
define("SPLASH_AUTHOR", 'Splash Official <www.splashsync.com>');

//====================================================================//
// NuSOAP Messaging Parameters
define("SPLASH_WS_METHOD", "SOAP");                 // Module Webservice Method
define("SPLASH_TIMEOUT", 30);                       // Client response timout in seconds
define("SPLASH_CRYPT_METHOD", "AES-256-CBC");       // Define Data Encryption Method
define("SPLASH_ENCODE", "XML");                     // Messages Encoding Format (XML, JSON)
define("SPLASH_CLASS_PREFIX", "\\Splash\\Local");   // Prefix To be Applied to all Local Class

//====================================================================//
// Defaults Parameters
define("SPLASH_LOCALPATH", "/..");                  // Relative Address to Local Library Folder
define("SPLASH_DF_LANG", "en_US");                  // Module Default Translation Language
define("SPLASH_LOGGING", false);                    // Enable activity logging on INI file
define("SPLASH_TRACE_IN", false);                   // Log Inputs Messages
define("SPLASH_TRACE_OUT", false);                  // Log Outputs Messages
define("SPLASH_TRACE_TASKS", false);                // Log Tasks Execution Events
define("SPLASH_SMART_NOTIFY", false);               // Smart Notifications => Only Warning & Errors on Commit Events
