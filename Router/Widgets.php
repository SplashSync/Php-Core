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
 * Server Request Routing Class, Execute/Route actions on Widgets Service Requests.
 * This file is included only in case on SOAP call to slave server.
 */
class Widgets implements RouterInterface
{
    /**
     * {@inheritDoc}
     *
     * @throws \Exception
     */
    public static function action(array $task): ?array
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        Splash::log()->deb("Widgets => ".$task['name']);
        //====================================================================//
        // Initial Response
        $response = Router::getEmptyResponse($task);

        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($task['name']) {
            case SPL_F_WIDGET_LIST:
                //====================================================================//
                //  READING OF SERVER WIDGETS LIST
                $response['data'] = Splash::widgets();

                break;
            case SPL_F_WIDGET_DEFINITION:
                //====================================================================//
                //  READING A WIDGET DEFINITION
                $response['data'] = Splash::widget($task['params']['type'] ?? "None")->description();

                break;
            case SPL_F_WIDGET_GET:
                //====================================================================//
                // Parse Widget Parameters
                $parameters = is_array($task['params']['params'] ?? null)
                    ? $task['params']['params']
                    : array()
                ;
                //====================================================================//
                //  READING A WIDGET CONTENTS
                $response['data'] = Splash::widget($task['params']['type'] ?? "None")
                    ->get($parameters)
                ;

                break;
            default:
                Splash::log()->err(
                    "Info Router - Requested task was not found => ".$task['name']." (".$task['desc'].")"
                );

                break;
        }
        //====================================================================//
        // Task results post treatment
        $response['result'] = !empty($response['data']);

        return $response;
    }
}
