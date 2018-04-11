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

/**
 * @abstract    Server Request Routiung Class, Execute/Route actions on Widgets Service Requests.
 *              This file is included only in case on NuSOAP call to slave server.
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Router;

use Splash\Core\SplashCore      as Splash;
use ArrayObject;

//====================================================================//
//   INCLUDES
//====================================================================//


//====================================================================//
//  CLASS DEFINITION
//====================================================================//
 
class Widgets
{
    /**
     *      @abstract   Task execution router. Receive task detail and execute requiered task operations.
     *
     *      @param      ArrayObject     $Task       Full Task Request Array
     *
     *      @return     ArrayObject                 Task results, or False if KO
     */
    public static function action($Task)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        Splash::log()->deb("Widgets => " . $Task->name);
        //====================================================================//
        // Initial Response
        $Response  = self::getEmptyResponse($Task);
        
        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($Task->name) {
            //====================================================================//
            //  READING OF SERVER WIDGETS LIST
            case SPL_F_WIDGET_LIST:
                $Response->data = Splash::widgets();
                break;
            
            //====================================================================//
            //  READING A WIDGET DEFINITION
            case SPL_F_WIDGET_DEFINITION:
                $WidgetClass    = Splash::widget($Task->params->type);
                if ($WidgetClass) {
                    $Response->data = $WidgetClass->description();
                }
                break;
                
            //====================================================================//
            //  READING A WIDGET CONTENTS
            case SPL_F_WIDGET_GET:
                $WidgetClass    = Splash::widget($Task->params->type);
                if ($WidgetClass) {
                    $Response->data = $WidgetClass->Get($Task->params->params);
                }
                break;

                
            default:
                Splash::log()->err(
                    "Info Router - Requested task was not found => " . $Task->name . " (" . $Task->desc . ")"
                );
                break;
        }
        
        //====================================================================//
        // Task results post treatment
        if ($Response->data != false) {
            $Response->result = true;
        }
        return $Response;
    }

    //====================================================================//
    //  LOW LEVEL FUNCTIONS
    //====================================================================//

    /**
     *      @abstract     Build an Empty Task Response
     *
     *      @param  ArrayObject     $Task       Task To Execute
     *
     *      @return ArrayObject   Task Result ArrayObject
     */
    private static function getEmptyResponse($Task)
    {
        //====================================================================//
        // Initial Tasks results ArrayObject
        $Response = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        
        //====================================================================//
        // Set Default Result to False
        $Response->result       =   false;
        $Response->data         =   null;
        
        //====================================================================//
        // Insert Task Description Informations
        $Response->name         =   $Task->name;
        $Response->desc         =   $Task->desc;

        return $Response;
    }
}
