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

namespace   Splash\Router;

use ArrayObject;
use Splash\Core\SplashCore      as Splash;

/**
 * Server Request Routiung Class, Execute/Route actions on Widgets Service Requests.
 * This file is included only in case on NuSOAP call to slave server.
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
class Widgets
{
    /**
     * Task execution router. Receive task detail and execute requiered task operations.
     *
     * @param ArrayObject $task Full Task Request Array
     *
     * @return ArrayObject Task results, or False if KO
     */
    public static function action($task)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        Splash::log()->deb("Widgets => ".$task->name);
        //====================================================================//
        // Initial Response
        $response = self::getEmptyResponse($task);

        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($task->name) {
            //====================================================================//
            //  READING OF SERVER WIDGETS LIST
            case SPL_F_WIDGET_LIST:
                $response->data = Splash::widgets();

                break;
            //====================================================================//
            //  READING A WIDGET DEFINITION
            case SPL_F_WIDGET_DEFINITION:
                $response->data = Splash::widget($task->params->type)->description();

                break;
            //====================================================================//
            //  READING A WIDGET CONTENTS
            case SPL_F_WIDGET_GET:
                $response->data = Splash::widget($task->params->type)->get($task->params->params);

                break;
            default:
                Splash::log()->err(
                    "Info Router - Requested task was not found => ".$task->name." (".$task->desc.")"
                );

                break;
        }

        //====================================================================//
        // Task results post treatment
        if (false != $response->data) {
            $response->result = true;
        }

        return $response;
    }

    //====================================================================//
    //  LOW LEVEL FUNCTIONS
    //====================================================================//

    /**
     * Build an Empty Task Response
     *
     * @param ArrayObject $task Task To Execute
     *
     * @return ArrayObject Task Result ArrayObject
     */
    private static function getEmptyResponse($task)
    {
        //====================================================================//
        // Initial Tasks results ArrayObject
        $response = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);

        //====================================================================//
        // Set Default Result to False
        $response->result = false;
        $response->data = null;

        //====================================================================//
        // Insert Task Description Informations
        $response->name = $task->name;
        $response->desc = $task->desc;

        return $response;
    }
}
