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

namespace Splash\Models;

/**
 * Communication Interface Class for Webservice Low Level Implementation
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
    public function buildClient(string $targetUrl, ?string $httpUser = null, ?string $httpPwd = null): self;

    /**
     * Execute WebService Client Request
     *
     * @param string $service Target Service
     * @param array  $data    Request Raw Data
     *
     * @return null|string Raw Response
     */
    public function call(string $service, array $data): ?string;

    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//

    /**
     * Create & Setup WebService Server
     *
     * @return void
     */
    public function buildServer(): void;

    /**
     * Responds to WebService Requests
     *
     * @return void
     */
    public function handle(): void;

    /**
     * Log Errors if Server fail during a request
     *
     * @param array $error
     *
     * @return void
     */
    public function fault(array $error): void;
}
