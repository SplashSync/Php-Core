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
     *      @param      ArrayObject     $Task       Full Task Request Array
     *
     *      @return     ArrayObject                 Task results, or False if KO
     */
    public static function action($Task)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        Splash::log()->deb("Object => " . $Task->name . " (" . $Task->desc . ")");
        
        //====================================================================//
        //  READING OF SERVER OBJECT LIST
        //====================================================================//
        if ($Task->name === SPL_F_OBJECTS) {
            return self::doObjects($Task);
        }
        
        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available
        if (!self::isValidTask($Task)) {
            return self::getEmptyResponse($Task);
        }
        
        //====================================================================//
        // Execute Admin Actions
        //====================================================================//
        if (in_array($Task->name, [ SPL_F_DESC , SPL_F_FIELDS , SPL_F_LIST ])) {
            return self::doAdminActions($Task);
        }
        if (in_array($Task->name, [ SPL_F_GET , SPL_F_SET , SPL_F_DEL ])) {
            return self::doSyncActions($Task);
        }
        //====================================================================//
        // Task Not Found
        Splash::log()->err("Objects - Requested task not found => " . $Task->name);
        return self::checkResponse(self::getEmptyResponse($Task));
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
    
    private static function checkResponse(&$Response)
    {
        if ($Response->data != false) {
            $Response->result   = true;
        }
        return $Response;
    }
    
    /**
     * @abstract   Verify Received Task
     *
     * @param      ArrayObject     $Task       Full Task Request Array
     *
     * @return     bool
     */
    private static function isValidTask($Task)
    {
        //====================================================================//
        // Verify Requested Object Type is Available
        if (empty($Task->params)) {
            Splash::log()->err("Object Router - Missing Task Parameters... ");
            return false;
        
        //====================================================================//
        // Verify Requested Object Type is Available
        } elseif (empty($Task->params->type)) {
            Splash::log()->err("Object Router - Missing Object Type... ");
            return false;
            
        //====================================================================//
        // Verify Requested Object Type is Valid
        } elseif (Splash::validate()->isValidObject($Task->params->type) != true) {
            Splash::log()->err("Object Router - Object Type is Invalid... ");
            return false;
        }

        return true;
    }
    
    private static function doAdminActions($Task)
    {
        //====================================================================//
        // Initial Response
        $Response  = self::getEmptyResponse($Task);
        
        //====================================================================//
        // Load Parameters
        $ObjectClass    = Splash::object($Task->params->type);
                                    
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
                $Filters            = isset($Task->params->filters) ?   $Task->params->filters  : null;
                $Params             = isset($Task->params->params)  ?   $Task->params->params   : null;
                $Response->data     = $ObjectClass->objectsList($Filters, $Params);
                break;
        }
        return self::checkResponse($Response);
    }
            
    private static function doSyncActions($Task)
    {
        //====================================================================//
        // Initial Response
        $Response  = self::getEmptyResponse($Task);
        
        //====================================================================//
        // Load Parameters
        $ObjectClass    = Splash::object($Task->params->type);
        $ObjectId       = isset($Task->params->id)      ?   $Task->params->id       : null;
        $Fields         = isset($Task->params->fields)  ?   $Task->params->fields   : null;

        //====================================================================//
        // Verify Object Id
        if (!Splash::validate()->isValidObjectId($ObjectId)) {
            return $Response;
        }
        
        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($Task->name) {
            //====================================================================//
            //  READING OF OBJECT DATA
            //====================================================================//
            case SPL_F_GET:
                $Response->data     =   self::doGet($ObjectClass, $ObjectId, $Fields);
                break;
            
            //====================================================================//
            //  WRITTING OF OBJECT DATA
            //====================================================================//
            case SPL_F_SET:
                $Response->data     =   self::doSet($ObjectClass, $ObjectId, $Fields);
                break;
                
            //====================================================================//
            //  DELETE OF AN OBJECT
            //====================================================================//
            case SPL_F_DEL:
                $Response->data     =   self::doDelete($ObjectClass, $ObjectId);
                break;
        }
        return self::checkResponse($Response);
    }
    
    private static function doObjects($Task)
    {
        //====================================================================//
        // Initial Response
        $Response  = self::getEmptyResponse($Task);
        
        //====================================================================//
        // Read Objects Types List from Local System
        $Response->data = Splash::objects();
        
        //====================================================================//
        // Return Response
        return self::checkResponse($Response);
    }
    
    private static function doGet(&$ObjectClass, $ObjectId, $Fields)
    {
        //====================================================================//
        // Verify Object Field List
        if (!Splash::validate()->isValidObjectFieldsList($Fields)) {
            return false;
        }
        
        //====================================================================//
        // Read Data fron local system
        return  $ObjectClass->get($ObjectId, $Fields);
    }
    
    private static function doSet(&$ObjectClass, $ObjectId, $Fields)
    {
        //====================================================================//
        // Take Lock for this object => No Commit Allowed for this Object
        $ObjectClass->lock($ObjectId);
        
        //====================================================================//
        // Write Data on local system
        $Data     =   $ObjectClass->set($ObjectId, $Fields);
        
        //====================================================================//
        // Release Lock for this object
        $ObjectClass->unLock($ObjectId);
        
        //====================================================================//
        // Return Response
        return $Data;
    }
    
    private static function doDelete(&$ObjectClass, $ObjectId)
    {
        //====================================================================//
        // Take Lock for this object => No Commit Allowed for this Object
        $ObjectClass->lock($ObjectId);
        
        //====================================================================//
        // Delete Data on local system
        return $ObjectClass->delete($ObjectId);
    }
}
