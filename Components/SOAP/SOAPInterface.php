<?php

/*
 *  This file is part of SplashSync Project.
 *
 *  Copyright (C) 2015-2021 Splash Sync  <www.splashsync.com>
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Splash\Components\SOAP;

use SoapClient;
use SoapFault;
use SoapServer;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\CommunicationInterface;

/**
 * Communication Interface Class for PHP SOAP Webservice
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
class SOAPInterface implements CommunicationInterface
{
    /**
     * @var string
     */
    protected $location;

    /**
     * @var string
     */
    protected $uri;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var SoapClient
     */
    protected $client;

    /**
     * @var SoapServer
     */
    protected $server;

    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function buildClient($targetUrl, $httpUser = null, $httpPwd = null)
    {
        //====================================================================//
        // Store Target Url
        $this->location = $targetUrl;

        //====================================================================//
        // Store Client Url
        $this->uri = Splash::input('SERVER_NAME')
                ? Splash::input('SERVER_NAME')
                : Splash::configuration()->WsHost;

        //====================================================================//
        // Build Options Array
        $this->options = array(
            'location' => $targetUrl,
            'uri' => $this->uri,
            'connection_timeout' => Splash::configuration()->WsTimout,
            'exceptions' => false,
        );

        //====================================================================//
        // Complete Options with Http Auth if Needed
        if (is_scalar($httpUser) && !empty($httpUser) && is_scalar($httpPwd) && !empty($httpPwd)) {
            $this->options["login"] = $httpUser;
            $this->options["password"] = $httpPwd;
        }

        //====================================================================//
        // Build Generic Soap Client
        $this->client = new SoapClient(null, $this->options);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function call($service, $data)
    {
        //====================================================================//
        // Log Call Informations in debug buffer
        Splash::log()->deb("[SOAP] Call Url= '".$this->location."' Service='".$service."'");
        //====================================================================//
        // Execute Php SOAP Call
        $response = $this->client->__soapCall($service, $data);
        //====================================================================//
        // Decode & Store Generic SOAP Errors if present
        if ($response instanceof SoapFault) {
            //====================================================================//
            //  Debug Informations
            Splash::log()->deb('[SOAP] Fault Details= '.$response->getTraceAsString());
            //====================================================================//
            //  Errro Message
            return Splash::log()->err('ErrWsNuSOAPFault', $response->getCode(), $response->getMessage());
        }

        return $response;
    }

    //====================================================================//
    // WEBSERVICE SERVER SIDE
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function buildServer()
    {
        //====================================================================//
        // Initialize Php SOAP Server Class
        $this->server = new SoapServer(null, array(
            'uri' => Splash::input('REQUEST_URI'),
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
     * {@inheritdoc}
     */
    public function handle()
    {
        if (isset($this->server)) {
            $this->server->handle((string) file_get_contents('php://input'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fault($error)
    {
        //====================================================================//
        // Prepare Fault Message.
        $content = 'SOAP call: service died unexpectedly!! ';
        $content .= $error['message'].' on File '.$error['file'].' Line '.$error['line'];
        //====================================================================//
        // Log Fault Details In SOAP Structure.
        $this->server->fault($error['type'], $content);
    }
}
