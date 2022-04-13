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

namespace Splash\Components\SOAP;

use Exception;
use SoapClient;
use SoapServer;
use Splash\Core\SplashCore      as Splash;
use Splash\Models\CommunicationInterface;

/**
 * Communication Interface Class for PHP SOAP Webservice
 */
class SOAPInterface implements CommunicationInterface
{
    /**
     * @var string
     */
    protected string $location;

    /**
     * @var string
     */
    protected string $uri;

    /**
     * @var array
     */
    protected array $options;

    /**
     * @var SoapClient
     */
    protected SoapClient $client;

    /**
     * @var SoapServer
     */
    protected SoapServer $server;

    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//

    /**
     * {@inheritdoc}
     */
    public function buildClient(string $targetUrl, ?string $httpUser = null, ?string $httpPwd = null): self
    {
        //====================================================================//
        // Store Target Url
        $this->location = $targetUrl;

        //====================================================================//
        // Store Client Url
        $this->uri = Splash::input('SERVER_NAME')
                ?: Splash::configuration()->WsHost;

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
    public function call(string $service, array $data): ?string
    {
        //====================================================================//
        // Log Call Informations in debug buffer
        Splash::log()->deb("[SOAP] Call Url= '".$this->location."' Service='".$service."'");
        //====================================================================//
        // Execute Php SOAP Call
        try {
            $response = $this->client->__soapCall($service, $data);
        } catch (Exception $exception) {
            $response = $exception;
        }
        //====================================================================//
        // Decode & Store Generic SOAP Errors if present
        if ($response instanceof Exception) {
            //====================================================================//
            //  Debug Informations
            Splash::log()->deb('[SOAP] Fault Details= '.$response->getTraceAsString());
            //====================================================================//
            //  Error Message
            return Splash::log()->errNull('ErrWsNuSOAPFault', $response->getCode(), $response->getMessage());
        }

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
    public function handle(): void
    {
        if (isset($this->server)) {
            $this->server->handle((string) file_get_contents('php://input'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fault(array $error): void
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
