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

use Splash\Server\SplashServer;

//====================================================================//
//   WebService Available Functions
//====================================================================//

/**
 * Minimal Test of Webservice connexion
 *
 * @return mixed WebService Packaged Data Outputs or NUSOAP Error
 */
function Ping()
{
    return SplashServer::ping();
}

/**
 * Connect Webservice and fetch server informations
 *
 * @param string $id   WebService Node Identifier
 * @param string $data WebService Packaged Data Inputs
 *
 * @return mixed WebService Packaged Data Outputs or NUSOAP Error
 */
function Connect($id, $data)
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
 * @return mixed WebService Packaged Data Outputs or NUSOAP Error
 */
function Admin($id, $data)
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
 * @return mixed WebService Packaged Data Outputs or NUSOAP Error
 */
function Objects($id, $data)
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
 * @return mixed WebService Packaged Data Outputs or NUSOAP Error
 */
function Files($id, $data)
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
 * @return mixed WebService Packaged Data Outputs or NUSOAP Error
 */
function Widgets($id, $data)
{
    $server = new SplashServer();

    return $server->widgets($id, $data);
}
