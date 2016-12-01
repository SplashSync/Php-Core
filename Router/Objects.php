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
    public static function Action($Task)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);   
        Splash::Log()->Deb("Object => " . $Task->name . " (" . $Task->desc . ")");
        
        //====================================================================//
        // Initial Response
        $Response  = self::getEmptyResponse($Task);
        
        //====================================================================//
        //  READING OF SERVER OBJECT LIST            
        //====================================================================//
        if ( $Task->name === SPL_F_OBJECTS ) {
            $Response->data = Splash::Objects();
            if ( $Response->data != False ) {
                $Response->result   = True;
            }
            return $Response;
        }
        
        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available 
        if (empty($Task->params)) {
            Splash::Log()->Err("Object Router - Missing Task Parameters... ");
            return $Response;
        
        //====================================================================//
        // Verify Requested Object Type is Available 
        } elseif (empty($Task->params->type)) {
            Splash::Log()->Err("Object Router - Missing Object Type... ");
            return $Response;
            
        //====================================================================//
        // Verify Requested Object Type is Valid
        } elseif (Splash::Validate()->isValidObject($Task->params->type) != True ) {
            Splash::Log()->Err("Object Router - Object Type is Invalid... ");
            return $Response;
        }
        
        //====================================================================//
        // Load Parameters
        $ObjectClass    = Splash::Object($Task->params->type);
        $ObjectId       = $Task->params->id;

        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($Task->name)
        {
            //====================================================================//
            //  READING OF Object Description            
            //====================================================================//
            case SPL_F_DESC :
                $Response->data     =   $ObjectClass->Description();
                break;
            
            //====================================================================//
            //  READING OF Available Fields            
            //====================================================================//
            case SPL_F_FIELDS :
                $Response->data     =   $ObjectClass->Fields();
                break; 
            
            //====================================================================//
            //  READING OF OBJECT LIST
            //====================================================================//
            case SPL_F_LIST:
                $Response->data     =   $ObjectClass->ObjectsList($Task->params->filters,$Task->params->params);
                break;
            
            //====================================================================//
            //  READING OF OBJECT DATA   
            //====================================================================//
            case SPL_F_GET:
                //====================================================================//
                // Verify Object Id 
                if (!Splash::Validate()->isValidObjectId($ObjectId)) {
                    break;
                }
                //====================================================================//
                // Verify Object Field List 
                if (!Splash::Validate()->isValidObjectFieldsList($Task->params->fields)) {
                    var_dump("Field List");
                    break;
                }
                $Response->data     =   $ObjectClass->Get($ObjectId,$Task->params->fields);
                break;
            
            //====================================================================//
            //  WRITTING OF OBJECT DATA             
            //====================================================================//
            case SPL_F_SET:
                //====================================================================//
                // Verify Object Field List 
                if (!Splash::Validate()->isValidObjectFieldsList($Task->params->fields)) {
                    break;
                }
                //====================================================================//
                // Take Lock for this object => No Commit Allowed for this Object
                $ObjectClass->Lock($ObjectId);                
                //====================================================================//
                //      Write Data on local system
                $Response->data     =   $ObjectClass->Set($ObjectId,$Task->params->fields);
                //====================================================================//
                // Release Lock for this object
                $ObjectClass->Unlock($ObjectId);
                break;
                
            //====================================================================//
            //  DELETE OF AN OBJECT             
            //====================================================================//
            case SPL_F_DEL:
                //====================================================================//
                // Verify Object Id 
                if (!Splash::Validate()->isValidObjectId($ObjectId)) {
                    break;
                }
                //====================================================================//
                // Take Lock for this object => No Commit Allowed for this Object
                $ObjectClass->Lock($ObjectId);
                //====================================================================//
                //      Delete Data on local system
                $Response->data     =   $ObjectClass->Delete($ObjectId);
                break;                 
                
            default:
                Splash::Log()->Err("Objects - Requested task not found => " . $Task->name);
                break;
        }  
        //====================================================================//
        // Task results prot treatment
        if ( $Response->data != False )  {   
            $Response->result = True; 
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
        $Response = new ArrayObject(array(),  ArrayObject::ARRAY_AS_PROPS);
        
        //====================================================================//
        // Set Default Result to False
        $Response->result       =   False;
        $Response->data         =   Null;
        
        //====================================================================//
        // Insert Task Description Informations
        $Response->name         =   $Task->name;
        $Response->desc         =   $Task->desc;

        return $Response;
    }         
}

?>