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
 * @abstract    Server Request Routiung Class, Execute/Route actions on Admin Service Requests.
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
 
class Admin
{
            
    /**
     *      @abstract   Task execution router. Receive task detail and execute requiered task operations.
     *
     *      @param      arrayobject     $Task       Full Task Request Array
     *
     *      @return     arrayobject                 Task results, or False if KO
     */
    public static function action($Task)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        Splash::log()->deb("Admin => " . $Task->name . " (" . $Task->desc . ")");
        
        //====================================================================//
        // Initial Response
        $Response  = self::getEmptyResponse($Task);
        
        switch ($Task->name) {
            //====================================================================//
            //  READING OF SERVER OBJECT LIST
            case SPL_F_GET_OBJECTS:
                $Response->data = Splash::objects();
                if ($Response->data != false) {
                    $Response->result   = true;
                }
                break;
                
            //====================================================================//
            //  READING OF SERVER WIDGETS LIST
            case SPL_F_GET_WIDGETS:
                $Response->data = Splash::widgets();
                if ($Response->data != false) {
                    $Response->result   = true;
                }
                break;
            
            //====================================================================//
            //  READING OF SERVER SELFTEST RESULTS
            case SPL_F_GET_SELFTEST:
                $Response->result  =   Splash::selfTest();
                $Response->data    =   $Response->result;
                break;
            
            //====================================================================//
            //  READING OF SERVER INFORMATIONS
            case SPL_F_GET_INFOS:
                $Response->data = Splash::informations();
                if ($Response->data != false) {
                    $Response->result   = true;
                }

                break;
                
            default:
                Splash::log()->err("Admin - Requested task not found => " . $Task->name);
                break;
        }
        
        return $Response;
    }
    
    //====================================================================//
    //  LOW LEVEL FUNCTIONS
    //====================================================================//

    /**
     *      @abstract     Build an Empty Task Response
     *
     *      @param  arrayobject     $Task       Task To Execute
     *
     *      @return arrayobject   Task Result ArrayObject
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
