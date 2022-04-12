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

namespace   Splash\Router;

use Splash\Components\Router;
use Splash\Core\SplashCore      as Splash;

/**
 * Server Request Routing Class, Execute/Route actions on Admin Service Requests.
 * This file is included only in case on NuSOAP call to slave server.
 */
class Admin implements RouterInterface
{
    /**
     * {@inheritDoc}
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
            //====================================================================//
            //  READING OF SERVER OBJECT LIST
            case SPL_F_GET_OBJECTS:
                $response['data'] = Splash::objects();
                $response['result'] = !empty($response['data']);

                break;
            //====================================================================//
            //  READING OF SERVER WIDGETS LIST
            case SPL_F_GET_WIDGETS:
                $response['data'] = Splash::widgets();
                $response['result'] = !empty($response['data']);

                break;
            //====================================================================//
            //  READING OF SERVER SELF-TEST RESULTS
            case SPL_F_GET_SELFTEST:
                $response['result'] = Splash::selfTest();
                $response['data'] = $response['result'];

                break;
            //====================================================================//
            //  READING OF SERVER INFORMATIONS
            case SPL_F_GET_INFOS:
                $response['data'] = Splash::informations();
                $response['result'] = !empty($response['data']);

                break;
            default:
                Splash::log()->err("Admin - Requested task not found => ".$task['name']);

                break;
        }

        return $response;
    }
}
