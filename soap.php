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

/**
 * This is Head include file for Splash PHP Module on WebService Request
 *
 * @author      B. Paquier <contact@splashsync.com>
 */

use Splash\Core\SplashCore      as Splash;
use Splash\Server\SplashServer;

//====================================================================//
// Disable Response Caching
header("Cache-Control: max-age=0, private, no-cache, no-store, must-revalidate, no-transform");

//====================================================================//
// Splash Module & Dependencies Autoloader
require_once(dirname(dirname(dirname(__FILE__)))."/autoload.php");
//====================================================================//
// Setup Php Specific Settings
ini_set('display_errors', "0");
error_reporting(E_ERROR);
//====================================================================//
// Notice internal routines we are in server request mode
define("SPLASH_SERVER_MODE", 1);
//====================================================================//
// Turn on output buffering
ob_start();

//====================================================================//
//  SERVER MODE - Answer NuSOAP Requests
//====================================================================//
// Detect NuSOAP requests send by Splash Server
$userAgent = Splash::input("HTTP_USER_AGENT");
if ($userAgent && (false !== strpos($userAgent, "SOAP"))) {
    //====================================================================//
    // Clean Output Buffer
    ob_clean();
    //====================================================================//
    //   Declare WebService Available Functions
    require_once(dirname(__FILE__)."/inc/server.inc.php");
    Splash::log()->deb("Splash Started In Server Mode");
    //====================================================================//
    // Build SOAP Server & Register a method available for clients
    Splash::com()->buildServer();
    //====================================================================//
    // Register shutdown method available for fatal errors retrieval
    register_shutdown_function(array(SplashServer::class, 'fatalHandler'));
    //====================================================================//
    // Clean Output Buffer
    ob_get_clean();
    ob_get_clean();
    //====================================================================//
    // Force UTF-8 Encoding & Protect against Varnish ESI Transform
    if (function_exists('getallheaders') && array_key_exists("X-Varnish", getallheaders())) {
        echo "\xEF\xBB\xBF";
    }
    //====================================================================//
    // Process methods & Return the results.
    Splash::com()->handle();
} elseif (Splash::input("node", INPUT_GET) === Splash::configuration()->WsIdentifier) {
    Splash::log()->deb("Splash Started In System Debug Mode");
    //====================================================================//
    // Turn on output buffering
    ob_end_flush();
    //====================================================================//
    // Setup Php Errors Settings
    ini_set('display_errors', "1");
    error_reporting(E_ALL);
    //====================================================================//
    // Force UTF-8 Encoding & Protect against Varnish ESI Transform
    if (function_exists('getallheaders') && array_key_exists("X-Varnish", getallheaders())) {
        echo "\xEF\xBB\xBF";
    }
    //====================================================================//
    // Output Server Analyze & Debug
    echo SplashServer::getStatusInformations();
    //====================================================================//
    // Output Module Complete Log
    echo Splash::log()->getHtmlLogList();
} else {
    echo "This WebService Provide no Description";
}
