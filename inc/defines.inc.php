<?php
/*
 * Copyright (C) 2011-2014  Bernard Paquier       <bernard.paquier@gmail.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 *
 *
 *  \Id 	$Id: main.lib.php 243 2013-06-02 16:05:41Z u58905340 $
 *  \version    $Revision: 243 $
 *  \date       $LastChangedDate$
 *  \ingroup    Splash Php Module
 *  \brief      Main constant definitions
 *  \remarks
*/

//====================================================================//
//====================================================================//
//  CONSTANTS DEFINITION
//====================================================================//
//====================================================================//

//====================================================================//
// Global Definitions
//====================================================================//
define("SPLASH_VERSION", '1.4.1');
define("SPLASH_NAME", 'Splash Php Client Module');
define("SPLASH_DESC", 'Splash Open-Source & Universal Synchronisation WebService.');
define("SPLASH_AUTHOR", 'Splash Official <www.splashsync.com>');

//====================================================================//
// NuSOAP Messaging Parameters
define("SPLASH_WS_METHOD", "NuSOAP");               // Module Webservice Method
define("SPLASH_TIMEOUT", 30);                       // Client response timout in seconds
define("SPLASH_CRYPT_METHOD", "AES-256-CBC");       // Define Data Encryption Method
define("SPLASH_ENCODE", "XML");                     // Messages Encoding Format (XML, JSON)
define("SPLASH_CLASS_PREFIX", "\Splash\Local");     // Prefix To be Applied to all Local Class

//====================================================================//
// Show Debug Messages
if (!defined('SPLASH_DEBUG')) {
    define("SPLASH_DEBUG", false);         // Activate Debug Mode or Not
}

//====================================================================//
// Defaults Parameters
define("SPLASH_LOCALPATH", "/..");             // Relative Address to Local Library Folder
define("SPLASH_DF_LANG", "en_US");           // Module Default Translation Language
define("SPLASH_LOGGING", false);             // Enable activity logging on INI file
define("SPLASH_TRACE_IN", false);             // Log Inputs Messages
define("SPLASH_TRACE_OUT", false);             // Log Outputs Messages
define("SPLASH_TRACE_TASKS", false);             // Log Tasks Execution Events
