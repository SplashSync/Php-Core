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

namespace Splash\Models;

/**
 * @abstract    Communication Interface Class for Webservice Low Level Implementation
 * @author      B. Paquier <contact@splashsync.com>
 */

interface CommunicationInterface
{
    
    
    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//
    
    /**
     * @abstract   Create & Setup WebService Client
     *
     * @param   string  $tagretUrl    Target Url
     *
     * @return self
     */
    public function buildClient($tagretUrl);
        
    /**
     * @abstract   Execute WebService Client Request
     *
     * @param string    $service   Target Service
     * @param string    $data      Request Raw Data
     *
     * @return     mixed    Raw Response
     */
    public function call($service, $data);
        
    //====================================================================//
    // WEBSERVICE CLIENT SIDE
    //====================================================================//
    
    /**
     * @abstract   Create & Setup WebService Server
     */
    public function buildServer();
    
    /**
     * @abstract   Responds to WebService Requests
     */
    public function handle();
    
    /**
     * @abstract   Log Errors if Server fail during a request
     */
    public function fault($error);
}
