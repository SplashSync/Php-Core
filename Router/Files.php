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
 * @abstract    Server Request Routiung Class, Execute/Route actions on Objects Service Requests.
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
 
class Files
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
        
        //====================================================================//
        // Initial Response
        $Response  = self::getEmptyResponse($Task);
        
        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available
        if (empty($Task->params)) {
            Splash::log()->err("File Router - Missing Task Parameters... ");
            return $Response;
        //====================================================================//
        // Verify Requested File Path is Available
        } elseif (empty($Task->params->path)) {
            Splash::log()->err("File Router - Missing File Path... ");
            return $Response;
        //====================================================================//
        // Verify Requested Object Type is Valid
        } elseif (Splash::validate()->isValidLocalClass() != true) {
            Splash::log()->err("File Router - Local Core Class is Invalid... ");
            return $Response;
        }
        
        //====================================================================//
        // Load Parameters
        $File           = $Task->params;
        
        switch ($Task->name) {
            //====================================================================//
            //  READING A FILE INFORMATIONS
            case SPL_F_ISFILE:
                $Response->data = Splash::file()->isFile($File->path, $File->md5);
                break;
            //====================================================================//
            //  READING A FILE CONTENTS
            case SPL_F_GETFILE:
                $Response->data = Splash::file()->readFile($File->path, $File->md5);
                break;
//            //====================================================================//
//            //  WRITE A FILE CONTENTS
//            case SPL_F_SETFILE:
//                $Response->data   = Splash::File()->WriteFile($File->path,$File->filename,$File->md5,$File->raw);
//                break;
//            //====================================================================//
//            //  DELETE A FILE
//            case SPl_F_DELFILE:
//                $Response->data   = Splash::File()->DeleteFile($File->path,$File->filename);
//                break;
            default:
                Splash::log()->err("File - Requested task not found => " . $Task->name);
                break;
        }
        
        //====================================================================//
        // Task results prot treatment
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
