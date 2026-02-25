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

namespace Splash\Core\Components\NuSOAP;

use nusoap_client;
use nusoap_server;
use Splash\Core\Client\Splash;
use Splash\Core\Dictionary\SplServices;
use Splash\Core\Interfaces\CommunicationInterface;

/**
 * Communication Interface Class for NuSOAP Webservice
 */
class NuSOAPInterface implements CommunicationInterface
{
    /**
     * @var nusoap_client
     */
    protected nusoap_client $client;

    /**
     * @var nusoap_server
     */
    protected ?nusoap_server $server = null;

    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function buildClient(string $targetUrl, ?string $httpUser = null, ?string $httpPwd = null): self
    {
        //====================================================================//
        // Include NuSOAP Classes
        require_once dirname(__DIR__, 3).'/libs/nusoap/nusoap.php';
        //====================================================================//
        // Initiate new NuSoap Client
        $this->client = new nusoap_client($targetUrl);
        //====================================================================//
        // Complete Options with Http Auth if Needed
        if (is_scalar($httpUser) && !empty($httpUser) && is_scalar($httpPwd) && !empty($httpPwd)) {
            $this->client->setCredentials($httpUser, $httpPwd);
        }
        //====================================================================//
        // Setup NuSOAP Debug Level
        if (Splash::isDebugMode()) {
            $this->client->setDebugLevel(2);
        }
        //====================================================================//
        // Setup NuSOAP Curl Option if Possible
        if (in_array('curl', get_loaded_extensions(), true)) {
            $this->client->setUseCURL(true);
        }
        //====================================================================//
        // Enable NuSOAP PersistentConnection
        $this->client->useHTTPPersistentConnection();
        //====================================================================//
        // Define Timeout for client response
        $this->client->response_timeout = Splash::configuration()->WsTimout;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function call(string $service, array $data): ?string
    {
        //====================================================================//
        // Log Call Information in debug buffer
        Splash::log()->deb("[NuSOAP] Call Url= '".$this->client->endpoint."' Service='".$service."'");
        //====================================================================//
        // Execute NuSOAP Call
        $response = $this->client->call($service, $data);
        //====================================================================//
        // Decode & Store NuSOAP Errors if present
        if (!empty($this->client->fault)) {
            //====================================================================//
            //  Debug Information
            Splash::log()->deb("[NuSOAP] Fault Details='".$this->client->faultdetail."'");
            //====================================================================//
            //  Log Error Message
            Splash::log()->err(
                'ErrWsNuSOAPFault',
                (string) $this->client->faultcode,
                (string) $this->client->faultstring
            );
        }
        //====================================================================//
        // Decode & Store NuSOAP Errors if present
        // if (empty($response)) {
        //     Splash::log()->err("[NuSOAP] Fault String='".((string) $this->client->error_str));
        //     Splash::log()->www("[NuSOAP] Raw Response", htmlspecialchars($this->client->response, ENT_QUOTES));
        // }

        return is_scalar($response) ? (string) $response : null;
    }

    //====================================================================//
    // WEBSERVICE SERVER SIDE
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function buildServer(): void
    {
        //====================================================================//
        // Include NuSOAP Classes
        require_once dirname(__DIR__, 3).'/libs/nusoap/nusoap.php';
        //====================================================================//
        // Initialize NuSOAP Server Class
        $this->server = new nusoap_server();
        //====================================================================//
        // Register a method available for clients
        $this->server->register(SplServices::PING);            // Check Availability
        $this->server->register(SplServices::CONNECT);         // Verify Connection Parameters
        $this->server->register(SplServices::ADMIN);           // Administrative requests
        $this->server->register(SplServices::OBJECTS);         // Main Object management requests
        $this->server->register(SplServices::FILE);            // Files management requests
        $this->server->register(SplServices::WIDGETS);         // Information requests
    }

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        if (isset($this->server)) {
            $this->server->service((string) file_get_contents('php://input'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fault(array $error): void
    {
        //====================================================================//
        // Safety Check
        if (empty($this->server)) {
            return;
        }
        //====================================================================//
        // Detect If Any Response Message Exists.
        if (!empty($this->server->response)) {
            return;
        }
        //====================================================================//
        // Prepare Fault Message.
        $content = 'NuSOAP call: service died unexpectedly!! ';
        $content .= $error['message'].' on File '.$error['file'].' Line '.$error['line'];
        //====================================================================//
        // Log Fault Details In SOAP Structure.
        $this->server->fault($error['type'], $content);
    }
}
