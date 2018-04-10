<?php
/*
 * This file is part of SplashSync Project.
 *
 * Copyright (C) Splash Sync <www.splashsync.com>
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace Splash;

/**
 * @abstract    This is Head include file for Splash PHP Module on WebService Request
 * @author      B. Paquier <contact@splashsync.com>
 */

use Splash\Core\SplashCore      as Splash;
use Splash\Server\SplashServer;

//====================================================================//
//   INCLUDES
//====================================================================//

//====================================================================//
// Splash Module & Dependecies Autoloader
require_once(dirname(dirname(dirname(__FILE__))) . "/autoload.php");

//====================================================================//
// Setup Php Specific Settings
ini_set('display_errors', 0);
error_reporting(E_ERROR);

//====================================================================//
// Notice internal routines we are in server request mode
define("SPLASH_SERVER_MODE", 1);
    
/*
 * @abstract   Declare fatal Error Handler => Called in case of Script Exceptions
 */
function fatal_handler()
{
    //====================================================================//
    // Read Last Error
    $Error  =   error_get_last();
    if (!$Error) {
        return;
    }
    //====================================================================//
    // Fatal Error
    if ($Error["type"] == E_ERROR) {
        //====================================================================//
        // Parse Error in Response.
        Splash::Com()->Fault($Error);
        //====================================================================//
        // Process methods & Return the results.
        Splash::Com()->Handle();
    //====================================================================//
    // Non Fatal Error
    } else {
        Splash::Log()->War($Error["message"] . " on File " . $Error["file"] . " Line " . $Error["line"]);
    }
}

//====================================================================//
//  SERVER MODE - Answer NuSOAP Requests
//====================================================================//
// Detect NuSOAP requests send by Splash Server
if (strpos(Splash::Input("HTTP_USER_AGENT"), "SOAP") !== false) {
    Splash::Log()->Deb("Splash Started In Server Mode");
            
    //====================================================================//
    //   WebService Available Functions
    //====================================================================//

    function Ping($id)
    {
        return SplashServer::Ping($id);
    }
    function Connect($id, $data)
    {
        $server = new SplashServer();
        return $server->Connect($id, $data);
    }
    function Admin($id, $data)
    {
        $server = new SplashServer();
        return $server->Admin($id, $data);
    }
    function Objects($id, $data)
    {
        $server = new SplashServer();
        return $server->Objects($id, $data);
    }
    function Files($id, $data)
    {
        $server = new SplashServer();
        return $server->Files($id, $data);
    }
    function Widgets($id, $data)
    {
        $server = new SplashServer();
        return $server->Widgets($id, $data);
    }

    //====================================================================//
    // Build SOAP Server & Register a method available for clients
    Splash::Com()->BuildServer();
    //====================================================================//
    // Register shuttdown method available for fatal errors reteival
    register_shutdown_function(__NAMESPACE__ . '\fatal_handler');
    //====================================================================//
    // Turn on output buffering
    ob_start();
    //====================================================================//
    // Process methods & Return the results.
    Splash::Com()->Handle();
} elseif (Splash::Input("node", INPUT_GET) === Splash::Configuration()->WsIdentifier) {
    Splash::Log()->Deb("Splash Started In System Debug Mode");
    //====================================================================//
    // Setup Php Errors Settings
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    //====================================================================//
    // Output Server Analyze & Debug
    echo SplashServer::GetStatusInformations();
    //====================================================================//
    // Output Module Complete Log
    echo Splash::Log()->GetHtmlLogList();
} else {
    echo "This WebService Provide no Description";
}
