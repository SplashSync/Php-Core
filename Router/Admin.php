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

use Exception;
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
            case SPL_F_GET_OBJECTS:
                //====================================================================//
                //  READING OF SERVER OBJECT LIST
                $response['data'] = Splash::objects();
                $response['result'] = !empty($response['data']);

                break;
            case SPL_F_GET_WIDGETS:
                //====================================================================//
                //  READING OF SERVER WIDGETS LIST
                $response['data'] = Splash::widgets();
                $response['result'] = !empty($response['data']);

                break;
            case SPL_F_GET_SELFTEST:
                //====================================================================//
                //  READING OF SERVER SELF-TEST RESULTS
                $response['result'] = Splash::selfTest();
                $response['data'] = $response['result'];

                break;
            case SPL_F_GET_INFOS:
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
