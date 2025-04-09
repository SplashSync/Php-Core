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

namespace Splash\Core\Server\Router;

use Exception;
use Splash\Core\Client\Splash;
use Splash\Core\Components\Router;
use Splash\Core\Dictionary\Methods\SplAdminMethods as Methods;
use Splash\Core\Interfaces\Server\RouterInterface;

/**
 * Server Request Routing Class, Execute/Route actions on Admin Service Requests.
 * This file is included only in case on NuSOAP call to slave server.
 */
class Admin implements RouterInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    public static function action(array $task): ?array
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        Splash::log()->deb("Admin => ".$task['name']." (".$task['desc'].")");

        //====================================================================//
        // Initial Response
        $response = Router::getEmptyResponse($task);

        switch ($task['name']) {
            case Methods::OBJECTS:
                //====================================================================//
                //  READING OF SERVER OBJECT LIST
                $response['data'] = Splash::objects();
                $response['result'] = !empty($response['data']);

                break;
            case Methods::WIDGETS:
                //====================================================================//
                //  READING OF SERVER WIDGETS LIST
                $response['data'] = Splash::widgets();
                $response['result'] = !empty($response['data']);

                break;
            case Methods::SELF_TEST:
                //====================================================================//
                //  READING OF SERVER SELF-TEST RESULTS
                $response['result'] = Splash::selfTest();
                $response['data'] = $response['result'];

                break;
            case Methods::INFOS:
                //====================================================================//
                //  READING OF SERVER INFORMATION
                $response['data'] = Splash::informations();
                $response['result'] = !empty($response['data']->count());

                break;
            default:
                Splash::log()->err("Admin - Requested task not found => ".$task['name']);

                break;
        }

        return $response;
    }
}
