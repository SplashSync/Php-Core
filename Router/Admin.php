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
 * @abstract    Server Request Routiung Class, Execute/Route actions on Admin Service Requests.
 *              This file is included only in case on NuSOAP call to slave server.
 *
 * @author      B. Paquier <contact@splashsync.com>
 */
class Admin
{
    /**
     *      @abstract   Task execution router. Receive task detail and execute requiered task operations.
     *
     *      @param      ArrayObject     $task       Full Task Request Array
     *
     *      @return     ArrayObject                 Task results, or False if KO
     */
    public static function action($task)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace();
        Splash::log()->deb("Admin => ".$task->name." (".$task->desc.")");

        //====================================================================//
        // Initial Response
        $response = self::getEmptyResponse($task);

        switch ($task->name) {
            //====================================================================//
            //  READING OF SERVER OBJECT LIST
            case SPL_F_GET_OBJECTS:
                $response->data = Splash::objects();
                if (false != $response->data) {
                    $response->result = true;
                }

                break;
            //====================================================================//
            //  READING OF SERVER WIDGETS LIST
            case SPL_F_GET_WIDGETS:
                $response->data = Splash::widgets();
                if (false != $response->data) {
                    $response->result = true;
                }

                break;
            //====================================================================//
            //  READING OF SERVER SELFTEST RESULTS
            case SPL_F_GET_SELFTEST:
                $response->result = Splash::selfTest();
                $response->data = $response->result;

                break;
            //====================================================================//
            //  READING OF SERVER INFORMATIONS
            case SPL_F_GET_INFOS:
                $response->data = Splash::informations();
                if (false != $response->data) {
                    $response->result = true;
                }

                break;
            default:
                Splash::log()->err("Admin - Requested task not found => ".$task->name);

                break;
        }

        return $response;
    }

    //====================================================================//
    //  LOW LEVEL FUNCTIONS
    //====================================================================//

    /**
     *      @abstract     Build an Empty Task Response
     *
     *      @param  ArrayObject     $task       Task To Execute
     *
     *      @return ArrayObject   Task Result ArrayObject
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
