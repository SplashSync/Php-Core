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

use Splash\Server\SplashServer;

//====================================================================//
//   WebService Available Functions
//====================================================================//

function Ping($id)
{
    return SplashServer::ping($id);
}
function Connect($id, $data)
{
    $server = new SplashServer();
    return $server->connect($id, $data);
}
function Admin($id, $data)
{
    $server = new SplashServer();
    return $server->admin($id, $data);
}
function Objects($id, $data)
{
    $server = new SplashServer();
    return $server->objects($id, $data);
}
function Files($id, $data)
{
    $server = new SplashServer();
    return $server->files($id, $data);
}
function Widgets($id, $data)
{
    $server = new SplashServer();
    return $server->widgets($id, $data);
}