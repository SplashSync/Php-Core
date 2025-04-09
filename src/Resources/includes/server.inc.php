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

use Splash\Core\Server\SplashServer;

//====================================================================//
//   WebService Available Functions
//====================================================================//

/**
 * Minimal Test of Webservice connexion
 *
 * @return null|string WebService Packaged Data Outputs or SOAP Error
 */
function Ping(): ?string
{
    return SplashServer::ping();
}

/**
 * Connect Webservice and fetch server information
 *
 * @param string $id   WebService Node Identifier
 * @param string $data WebService Packaged Data Inputs
 *
 * @return null|string WebService Packaged Data Outputs or SOAP Error
 */
function Connect(string $id, string $data): ?string
{
    $server = new SplashServer();

    return $server->connect($id, $data);
}

/**
 * Administrative server functions
 *
 * @param string $id   WebService Node Identifier
 * @param string $data WebService Packaged Data Inputs
 *
 * @return null|string WebService Packaged Data Outputs or NUSOAP Error
 */
function Admin(string $id, string $data): ?string
{
    $server = new SplashServer();

    return $server->admin($id, $data);
}

/**
 * Objects server functions
 *
 * @param string $id   WebService Node Identifier
 * @param string $data WebService Packaged Data Inputs
 *
 * @return null|string WebService Packaged Data Outputs or NUSOAP Error
 */
function Objects(string $id, string $data): ?string
{
    $server = new SplashServer();

    return $server->objects($id, $data);
}

/**
 * Files Transfers server functions
 *
 * @param string $id   WebService Node Identifier
 * @param string $data WebService Packaged Data Inputs
 *
 * @return null|string WebService Packaged Data Outputs or NUSOAP Error
 */
function Files(string $id, string $data): ?string
{
    $server = new SplashServer();

    return $server->files($id, $data);
}

/**
 * Widgets Retrieval server functions
 *
 * @param string $id   WebService Node Identifier
 * @param string $data WebService Packaged Data Inputs
 *
 * @return null|string WebService Packaged Data Outputs or NUSOAP Error
 */
function Widgets(string $id, string $data): ?string
{
    $server = new SplashServer();

    return $server->widgets($id, $data);
}
