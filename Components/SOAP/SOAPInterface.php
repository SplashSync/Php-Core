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

namespace Splash\Components\SOAP;

use Splash\Core\SplashCore      as Splash;
use Splash\Models\CommunicationInterface;

/**
 * @abstract    Communication Interface Class for PHP SOAP Webservice
 * @author      B. Paquier <contact@splashsync.com>
 */
class SOAPInterface implements CommunicationInterface
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
    public function buildClient($Url)
    {
        $this->client = new \SoapClient(null, array(
            'location'              =>  $Url,
            'uri'                   =>  Splash::input("SERVER_NAME"),
            'connection_timeout'    =>  Splash::configuration()->WsTimout,
            'exceptions'            =>  false,
        ));
    }
        
    /**
     * @abstract   Execute WebService Client Request
     *
     * @param string    $Service   Target Service
     * @param string    $Data      Request Raw Data
     *
     * @return     mixed    Raw Response
     */
    public function call($Service, $Data)
    {
        //====================================================================//
        // Log Call Informations in debug buffer
        Splash::log()->deb("[SOAP] Call Url= '" . $this->client->location . "' Service='" . $Service . "'");
        //====================================================================//
        // Execute Php SOAP Call
        $Response = $this->client->__soapCall($Service, $Data);
        return $Response;
    }
        
    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//
    
    /**
     * @abstract   Create & Setup WebService Server
     */
    public function buildServer()
    {
        //====================================================================//
        // Initialize Php SOAP Server Class
        $this->server           = new \SoapServer(null, array(
            'uri' => Splash::input("REQUEST_URI"),
                ));
        //====================================================================//
        // Register a method available for clients
        $this->server->addFunction(SPL_S_PING);        // Check Slave Availability
        $this->server->addFunction(SPL_S_CONNECT);      // Verify Connection Parameters
        $this->server->addFunction(SPL_S_ADMIN);        // Administrative requests
        $this->server->addFunction(SPL_S_OBJECTS);      // Main Object management requests
        $this->server->addFunction(SPL_S_FILE);         // Files management requests
        $this->server->addFunction(SPL_S_WIDGETS);      // Informations requests
    }
    
    /**
     * @abstract   Responds to WebService Requests
     */
    public function handle()
    {
        if (isset($this->server)) {
            $this->server->handle(file_get_contents('php://input'));
        }
    }
    
    /**
     * @abstract   Log Errors if Server fail during a request
     */
    public function fault($Error)
    {
        //====================================================================//
        // Prepare Fault Message.
        $content  = "SOAP call: service died unexpectedly!! ";
        $content .= $Error["message"] . " on File " . $Error["file"] . " Line " . $Error["line"];
        //====================================================================//
        // Log Fault Details In SOAP Structure.
        $this->server->fault($Error["type"], $content);
    }
}
