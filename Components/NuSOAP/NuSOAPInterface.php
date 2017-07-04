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

namespace Splash\Components\NuSOAP;

use Splash\Core\SplashCore      as Splash;
use Splash\Models\CommunicationInterface;

/**
 * @abstract    Communication Interface Class for NuSOAP Webservice
 * @author      B. Paquier <contact@splashsync.com>
 */
class NuSOAPInterface implements CommunicationInterface
{
    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//        
    
    /**
     * @abstract   Create & Setup WebService Client
     * 
     * @param   string  $Url    Target Url
     * 
     * @return self
     */
    public function BuildClient($Url)
    {
        //====================================================================//
        // Include NuSOAP Classes
        require_once( dirname(__FILE__) . "/nusoap.php");        
        //====================================================================//
        // Initiate new NuSoap Client
        $this->client = new \nusoap_client($Url);
        //====================================================================//
        // Setup NuSOAP Debug Level
        if (SPLASH_DEBUG) {
            $this->client->setDebugLevel(2);
        }
        //====================================================================//
        // Setup NuSOAP Curl Option if Possible
        if  (in_array  ('curl', get_loaded_extensions())) {
            $this->client->setUseCURL(true);
        }
        //====================================================================//
        // Enable NuSOAP PersistentConnection
        $this->client->useHTTPPersistentConnection();
        //====================================================================//
        // Define Timeout for client response
        $this->client->response_timeout = Splash::Configuration()->WsTimout;
    }
        
    /**
     * @abstract   Execute WebService Client Request
     * 
     * @param string    $Service   Target Service
     * @param string    $Data      Request Raw Data
     * 
     * @return     mixed    Raw Response
     */
    public function Call($Service, $Data)
    {
        //====================================================================//
        // Log Call Informations in debug buffer
        Splash::Log()->Deb("[NuSOAP] Call Url= '" . $this->client->endpoint . "' Service='" . $Service . "'");        
        //====================================================================//
        // Execute NuSOAP Call
        $Response = $this->client->call($Service, $Data);
        //====================================================================//
        // Decode & Store NuSOAP Errors if present
        if ( isset($this->client->fault) && !empty($this->client->fault) ) {
            //====================================================================//
            //  Debug Informations            
            Splash::Log()->Deb("[NuSOAP] Fault Details='"   . $this->client->faultdetail . "'");
            //====================================================================//
            //  Log Error Message
            Splash::Log()->Err("ErrWsNuSOAPFault",$this->client->faultcode ,$this->client->faultstring);
        }

        return $Response;
    }
        
    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//        
    
    /**
     * @abstract   Create & Setup WebService Server
     */
    public function BuildServer()        
    {
        //====================================================================//
        // Include NuSOAP Classes
        require_once( dirname(__FILE__) . "/nusoap.php");
        //====================================================================//
        // Initialize NuSOAP Server Class
        $this->server           = new \soap_server();
        //====================================================================//
        // Register a method available for clients
        $this->server->register(SPL_S_PING);           // Check Availability
        $this->server->register(SPL_S_CONNECT);         // Verify Connection Parameters
        $this->server->register(SPL_S_ADMIN);           // Administrative requests
        $this->server->register(SPL_S_OBJECTS);         // Main Object management requests
        $this->server->register(SPL_S_FILE);            // Files management requests
        $this->server->register(SPL_S_WIDGETS);         // Informations requests                
    }
    
    /**
     * @abstract   Responds to WebService Requests
     */
    public function Handle()        
    {
        if ( isset($this->server) ) {
            $this->server->service(file_get_contents('php://input'));
        }
    }
    
    /**
     * @abstract   Log Errors if Server fail during a request
     */
    public function Fault($Error)     
    {
        //====================================================================//
        // Detect If Any Response Message Exists.
        if( !empty($this->server->response) ) { return; }
        //====================================================================//
        // Prepare Fault Message.
        $content  = "NuSOAP call: service died unexpectedly!! ";
        $content .= $Error["message"] . " on File " . $Error["file"] . " Line " . $Error["line"];
        //====================================================================//
        // Log Fault Details In SOAP Structure.
        $this->server->fault($Error["type"], $content);   
    }
    
}
