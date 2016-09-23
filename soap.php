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

/**
 * @abstract    This is Head include file for Splash PHP Module on WebService Request
 * @author      B. Paquier <contact@splashsync.com>
 */

//====================================================================//
//   INCLUDES 
//====================================================================//  

//====================================================================//
// Declare fatal Error Handler => Called in case of Script Exceptions
function Splash_fatal_handler() {
    
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
if ( strpos(filter_input(INPUT_SERVER, "HTTP_USER_AGENT") , "NuSOAP" ) !== FALSE )
//if ( TRUE )
{
    //====================================================================//
    // Setup Php Specific Settings
    ini_set('display_errors', 0);
    error_reporting(E_ERROR);
    
    //====================================================================//
    // Save Library Home folder Detected
    define('SPLASH_DIR' , dirname(__FILE__));
    
    //====================================================================//  
    // Notice internal routines we are in server request mode
    define("SPLASH_SERVER_MODE"   ,   1);    
    
    //====================================================================//
    // Splash Constants Definitions
    if (!defined('SPL_PROTOCOL')) {
        require_once(SPLASH_DIR."/inc/Splash.Inc.php");
    }
    
    //====================================================================//
    // Include Required Files ==> Server Class
    require_once(SPLASH_DIR."/class/SplashServer.php");
    Splash::Log()->Deb("Splash Started In Server Mode");    
    
    //====================================================================//
    // Register a method available for clients
    SplashServer::Register();
    
    //====================================================================//
    // Register shuttdown method available for fatal errors reteival
    register_shutdown_function( 'Splash_fatal_handler' );
    ob_start();                     // Turn on output buffering
    
    //====================================================================//
    // Process methods & Return the results.
    Splash::Log()->ddd("Received Data",file_get_contents('php://input'));     
    Splash::Server()->service(file_get_contents('php://input'));
    
} else {
    echo "This WebService Provide no Description.";
}
//====================================================================//  
?>