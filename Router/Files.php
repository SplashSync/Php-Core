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


namespace   Splash\Router;

use Splash\Core\SplashCore      as Splash;
use ArrayObject;

/**
 * @abstract    Server Request Routiung Class, Execute/Route actions on Objects Service Requests.
 *              This file is included only in case on NuSOAP call to slave server.
 * @author      B. Paquier <contact@splashsync.com>
 */
class Files
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
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        
        //====================================================================//
        // Initial Response
        $response  = self::getEmptyResponse($task);
        
        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available
        if (empty($task->params)) {
            Splash::log()->err("File Router - Missing Task Parameters... ");
            return $response;
        //====================================================================//
        // Verify Requested File Path is Available
        } elseif (empty($task->params->path) && empty($task->params->file)) {
            Splash::log()->err("File Router - Missing File Path... ");
            return $response;
        //====================================================================//
        // Verify Requested Object Type is Valid
        } elseif (Splash::validate()->isValidLocalClass() != true) {
            Splash::log()->err("File Router - Local Core Class is Invalid... ");
            return $response;
        }
        
        //====================================================================//
        // Load Parameters
        $path   = empty($task->params->path) ? $task->params->file : $task->params->path;
        $md5    = $task->params->md5;
        
        //====================================================================//
        // Execute Action
        switch ($task->name) {
            //====================================================================//
            //  READING A FILE INFORMATIONS
            case SPL_F_ISFILE:
                $response->data = Splash::file()->isFile($path, $md5);
                break;
            //====================================================================//
            //  READING A FILE CONTENTS
            case SPL_F_GETFILE:
                $response->data = Splash::file()->readFile($path, $md5);
                break;
            default:
                Splash::log()->err("File - Requested task not found => " . $task->name);
                break;
        }
        
        //====================================================================//
        // Task results prot treatment
        if ($response->data != false) {
            $response->result = true;
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
        $response->result       =   false;
        $response->data         =   null;
        
        //====================================================================//
        // Insert Task Description Informations
        $response->name         =   $task->name;
        $response->desc         =   $task->desc;

        return $response;
    }
}
