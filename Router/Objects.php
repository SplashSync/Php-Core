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
 
class Objects
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
        Splash::log()->deb("Object => " . $Task->name . " (" . $Task->desc . ")");
        
        //====================================================================//
        // Initial Response
        $Response  = self::getEmptyResponse($Task);
        
        //====================================================================//
        //  READING OF SERVER OBJECT LIST
        //====================================================================//
        if ($Task->name === SPL_F_OBJECTS) {
            $Response->data = Splash::objects();
            if ($Response->data != false) {
                $Response->result   = true;
            }
            return $Response;
        }
        
        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available
        if (empty($Task->params)) {
            Splash::log()->err("Object Router - Missing Task Parameters... ");
            return $Response;
        
        //====================================================================//
        // Verify Requested Object Type is Available
        } elseif (empty($Task->params->type)) {
            Splash::log()->err("Object Router - Missing Object Type... ");
            return $Response;
            
        //====================================================================//
        // Verify Requested Object Type is Valid
        } elseif (Splash::validate()->isValidObject($Task->params->type) != true) {
            Splash::log()->err("Object Router - Object Type is Invalid... ");
            return $Response;
        }
        
        //====================================================================//
        // Load Parameters
        $ObjectClass    = Splash::object($Task->params->type);
        $ObjectId       = isset($Task->params->id) ? $Task->params->id : null;

        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($Task->name) {
            //====================================================================//
            //  READING OF Object Description
            //====================================================================//
            case SPL_F_DESC:
                $Response->data     =   $ObjectClass->description();
                break;
            
            //====================================================================//
            //  READING OF Available Fields
            //====================================================================//
            case SPL_F_FIELDS:
                $Response->data     =   $ObjectClass->fields();
                break;
            
            //====================================================================//
            //  READING OF OBJECT LIST
            //====================================================================//
            case SPL_F_LIST:
                $Response->data     =   $ObjectClass->objectsList(
                    isset($Task->params->filters)   ?   $Task->params->filters  : null,
                    isset($Task->params->params)    ?   $Task->params->params   : null
                );
                break;
            
            //====================================================================//
            //  READING OF OBJECT DATA
            //====================================================================//
            case SPL_F_GET:
                //====================================================================//
                // Verify Object Id
                if (!Splash::validate()->isValidObjectId($ObjectId)) {
                    break;
                }
                //====================================================================//
                // Verify Object Field List
                if (!Splash::validate()->isValidObjectFieldsList($Task->params->fields)) {
                    break;
                }
                $Response->data     =   $ObjectClass->get($ObjectId, $Task->params->fields);
                break;
            
            //====================================================================//
            //  WRITTING OF OBJECT DATA
            //====================================================================//
            case SPL_F_SET:
                //====================================================================//
                // Verify Object Field List
                if (!Splash::validate()->isValidObjectFieldsList($Task->params->fields)) {
                    break;
                }
                //====================================================================//
                // Take Lock for this object => No Commit Allowed for this Object
                $ObjectClass->lock($ObjectId);
                //====================================================================//
                //      Write Data on local system
                $Response->data     =   $ObjectClass->set($ObjectId, $Task->params->fields);
                //====================================================================//
                // Release Lock for this object
                $ObjectClass->unLock($ObjectId);
                break;
                
            //====================================================================//
            //  DELETE OF AN OBJECT
            //====================================================================//
            case SPL_F_DEL:
                //====================================================================//
                // Verify Object Id
                if (!Splash::validate()->isValidObjectId($ObjectId)) {
                    break;
                }
                //====================================================================//
                // Take Lock for this object => No Commit Allowed for this Object
                $ObjectClass->lock($ObjectId);
                //====================================================================//
                //      Delete Data on local system
                $Response->data     =   $ObjectClass->delete($ObjectId);
                break;
                
            default:
                Splash::log()->err("Objects - Requested task not found => " . $Task->name);
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
