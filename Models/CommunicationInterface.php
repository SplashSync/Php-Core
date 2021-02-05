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

namespace Splash\Models;

/**
 * Communication Interface Class for Webservice Low Level Implementation
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
interface CommunicationInterface
{
    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//

    /**
     * Create & Setup WebService Client
     *
     * @param string      $targetUrl Target Url
     * @param null|string $httpUser  User for Http Authentification
     * @param null|string $httpPwd   Password for Http Authentification
     *
     * @return self
     */
    public function buildClient($targetUrl, $httpUser = null, $httpPwd = null);

    /**
     * Execute WebService Client Request
     *
     * @param string $service Target Service
     * @param array  $data    Request Raw Data
     *
     * @return mixed Raw Response
     */
    public function call($service, $data);

    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//

    /**
     * Create & Setup WebService Server
     *
     * @return void
     */
    public function buildServer();

    /**
     * Responds to WebService Requests
     *
     * @return void
     */
    public function handle();

    /**
     * Log Errors if Server fail during a request
     *
     * @param mixed $error
     *
     * @return void
     */
    public function fault($error);
}
