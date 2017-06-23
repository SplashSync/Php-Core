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
require_once( dirname(dirname(dirname(__FILE__))) . "/autoload.php");

//====================================================================//
// Setup Php Specific Settings
ini_set('display_errors', 0);
error_reporting(E_ERROR);    
    
//====================================================================//  
// Notice internal routines we are in server request mode
define("SPLASH_SERVER_MODE"   ,   1);    
    
//====================================================================//
// Declare fatal Error Handler => Called in case of Script Exceptions
function fatal_handler() {
    
   //====================================================================//
   // Detect If Any Response Message Exists.
   if( !empty(Splash::Server()->response) ) { return; }
   
   //====================================================================//
   // Prepare Fault Message.
   $error = error_get_last();
   $content  = "NuSOAP call: service died unexpectedly!! ";
   $content .= $error["message"] . " on File " . $error["file"] . " Line " . $error["line"];
   
   //====================================================================//
   // Log Fault Details In NuSOAP Structure.
   Splash::Server()->fault($error["type"], $content);
   
   //====================================================================//
   // Process methods & Return the results.
   Splash::Server()->service(file_get_contents('php://input'));
   
   return;
} 

//====================================================================//  
//  SERVER MODE - Answer NuSOAP Requests
//====================================================================//  
// Detect NuSOAP requests send by Splash Server 
if ( strpos(Splash::Input("HTTP_USER_AGENT") , "NuSOAP" ) !== FALSE )
{
    
    //====================================================================//
    //   WebService Available Functions
    //====================================================================//  

    function Ping($id)                      {   return SplashServer::Ping($id);     }
    function Connect($id,$data)             {   $server = new SplashServer(); return $server->Connect($id,$data);   }
    function Admin($id,$data)               {   $server = new SplashServer(); return $server->Admin($id,$data);     }
    function Objects($id,$data)             {   $server = new SplashServer(); return $server->Objects($id,$data);   }
    function Files($id,$data)               {   $server = new SplashServer(); return $server->Files($id,$data);     }
    function Widgets($id,$data)             {   $server = new SplashServer(); return $server->Widgets($id,$data);   }

    Splash::Log()->Deb("Splash Started In Server Mode");    
    
    //====================================================================//
    // Register a method available for clients
    SplashServer::Register();

    //====================================================================//
    // Register shuttdown method available for fatal errors reteival
    register_shutdown_function( __NAMESPACE__ . '\fatal_handler' );
    //====================================================================//
    // Turn on output buffering
    ob_start();                     
    
    //====================================================================//
    // Process methods & Return the results.
    Splash::Log()->ddd("Received Data",file_get_contents('php://input'));     
    Splash::Server()->service(file_get_contents('php://input'));
    

} elseif ( Splash::Input("node", INPUT_GET) === Splash::Configuration()->WsIdentifier ) {
    echo "Server Informations";
    echo "<PRE>";
    print_r(Splash::Ws()->getServerInfos());
    echo "</PRE>";
} else {
    echo "This WebService Provide no Description.";
}
