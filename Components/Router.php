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
 * @abstract    Server Request Routiung Class, Execute/Route actions uppon Splash Server requests. 
 *              This file is included only in case on NuSOAP call to slave server.
 * @author      B. Paquier <contact@splashsync.com>
 */

namespace   Splash\Components;

use Splash\Core\SplashCore      as Splash;
use ArrayObject;

//====================================================================//
//   INCLUDES 
//====================================================================//  


//====================================================================//
//  CLASS DEFINITION
//====================================================================//  
 
class Router 
{
    
    //====================================================================//
    // Tasks Counters
    private $Count  =   0;                  // Input Task Counter
    private $Ok     =   0;                  // Succeeded Task Counter

    //====================================================================//
    // Tasks Statistics
    private $BatchTimer;                    // Task Batch Execution Start Timestamp
    private $TaskTimer;                          // Current Task Execution Start Timestamp
    
//====================================================================//
//  SERVER TASKING FUNCTIONS
//====================================================================//
        
    /**
     *      @abstract   Task execution router. Receive task detail and execute requiered task operations. 
     * 
     *      @param      arrayobject     $Task       Full Task Request Array
     * 
     *      @return     arrayobject                 Task results, or False if KO
     */
    private function Admin($Task)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);   
        Splash::Log()->Deb("Admin => " . $Task->name . " (" . $Task->desc . ")");
        
        //====================================================================//
        // Initial Response
        $Response  = $this->getEmptyResponse($Task);
        
        switch ($Task->name)
        {
            //====================================================================//
            //  READING OF SERVER OBJECT LIST            
            case SPL_F_GET_OBJECTS:
                $Response->data = Splash::Objects();
                if ( $Response->data != False ) {
                    $Response->result   = True;
                }
                break; 
                
            //====================================================================//
            //  READING OF SERVER WIDGETS LIST            
            case SPL_F_GET_WIDGETS:
                $Response->data = Splash::Widgets();
                if ( $Response->data != False ) {
                    $Response->result   = True;
                }
                break;  
            
            //====================================================================//
            //  READING OF SERVER SELFTEST RESULTS            
            case SPL_F_GET_SELFTEST:
                $Response->result  =   Splash::SelfTest();
                $Response->data    =   $Response->result;
                break;
            
            //====================================================================//
            //  READING OF SERVER INFORMATIONS            
            case SPL_F_GET_INFOS:
                $Response->data = Splash::Informations();
                if ( $Response->data != False ) {
                    $Response->result   = True;
                }

                break;             
                
            default:
                Splash::Log()->Err("Admin - Requested task not found => " . $Task->name);
                break;
        }
        
        return $Response;        
    }
    
    /**
     *      @abstract   Task execution router. Receive task detail and execute requiered task operations. 
     * 
     *      @param      arrayobject     $Task       Full Task Request Array
     * 
     *      @return     arrayobject                 Task results, or False if KO
     */
    private function Objects($Task)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);   
        Splash::Log()->Deb("Object => " . $Task->name . " (" . $Task->desc . ")");
        
        //====================================================================//
        // Initial Response
        $Response  = $this->getEmptyResponse($Task);

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
     
    /**
     *      @abstract   Task execution router. Receive task detail and execute requiered task operations. 
     * 
     *      @param      arrayobject     $Task       Full Task Request Array
     * 
     *      @return     arrayobject                 Task results, or False if KO
     */
    private function Files($Task)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);   
        Splash::Log()->War("File => " . $Task->name );
        
        //====================================================================//
        // Initial Response
        $Response  = $this->getEmptyResponse($Task);
        
        //====================================================================//
        // Safety Check - Minimal Parameters
        //====================================================================//
        // Verify Requested Object Type is Available 
        if (empty($Task->params)) {
            Splash::Log()->Err("File Router - Missing Task Parameters... ");
            return $Response;
        //====================================================================//
        // Verify Requested File Name is Available 
        } elseif (empty($Task->params->filename)) {
            Splash::Log()->Err("File Router - Missing FileName... ");
            return $Response;
        //====================================================================//
        // Verify Requested File Path is Available 
        } elseif (empty($Task->params->path)) {
            Splash::Log()->Err("File Router - Missing File Path... ");
            return $Response;
        //====================================================================//
        // Verify Requested Object Type is Valid
        } elseif (Splash::Validate()->isValidLocalClass() != True ) {
            Splash::Log()->Err("File Router - Local Core Class is Invalid... ");
            return $Response;
        }
        
        //====================================================================//
        // Load Parameters
        $File           = $Task->params;
        
        switch ($Task->name)
        {
            //====================================================================//
            //  READING A FILE INFORMATIONS
            case SPL_F_ISFILE:
                $Response->data	= Splash::File()->is_File($File->path,$File->filename);
                break;
            //====================================================================//
            //  READING A FILE CONTENTS
            case SPL_F_GETFILE:
                $Response->data	= Splash::File()->ReadFile($File->path,$File->filename);
                break;
//            //====================================================================//
//            //  WRITE A FILE CONTENTS
//            case SPL_F_SETFILE:
//                $Response->data	= Splash::File()->WriteFile($File->path,$File->filename,$File->md5,$File->raw);
//                break;  
//            //====================================================================//
//            //  DELETE A FILE
//            case SPl_F_DELFILE:
//                $Response->data	= Splash::File()->DeleteFile($File->path,$File->filename);
//                break;
            default:
                Splash::Log()->Err("File - Requested task not found => " . $Task->name );
                break;
        }
        
        //====================================================================//
        // Task results prot treatment
        if ( $Response->data != False )  {   
            $Response->result = True; 
        }
        return $Response;            
    }
 
    /**
     *      @abstract   Task execution router. Receive task detail and execute requiered task operations. 
     * 
     *      @param      arrayobject     $Task       Full Task Request Array
     * 
     *      @return     arrayobject                 Task results, or False if KO
     */
    private function Widgets($Task)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);   
        Splash::Log()->Deb("Widgets => " . $Task->name );
        //====================================================================//
        // Initial Response
        $Response  = $this->getEmptyResponse($Task);
        
        //====================================================================//
        // Execute Requested Task
        //====================================================================//
        switch ($Task->name)
        {
            //====================================================================//
            //  READING OF SERVER WIDGETS LIST            
            case SPL_F_WIDGET_LIST:
                $Response->data = Splash::Widgets();
                break;             
            
            //====================================================================//
            //  READING A WIDGET DEFINITION
            case SPL_F_WIDGET_DEFINITION:
                $WidgetClass    = Splash::Widget($Task->params->type);
                if ( $WidgetClass ) {
                    $Response->data	= $WidgetClass->Description();
                }
                break;
                
            //====================================================================//
            //  READING A WIDGET CONTENTS
            case SPL_F_WIDGET_GET:
                $WidgetClass    = Splash::Widget($Task->params->type);
                if ( $WidgetClass ) {
                    $Response->data	= $WidgetClass->Get($Task->params->params);
                }
                break;

                
            default:
                Splash::Log()->Err("Info Router - Requested task was not found => " . $Task->name . " (" . $Task->desc . ")");
                break;                
        }
        
        //====================================================================//
        // Task results post treatment
        if ( $Response->data != False )  {   
            $Response->result = True; 
        }
        return $Response;        
    }

//====================================================================//
//  SERVER TASKING MANAGER
//====================================================================//

    /**
     *      @abstract     Validate Received Server Request
     *  
     *      @param  string          $Router     Name of the router function to use for task execution
     *      @param  arrayobject     $Input      Poiner to Server Input Buffer
     *      @param  arrayobject     $Output     Poiner to Server Output Buffer
     * 
     *      @return bool            
     */
    private function Validate($Router,$Input,$Output)
    {
        //====================================================================//
        // Safety Checks - Verify Inputs & Outputs are Valid
        if ( !is_a($Input,"ArrayObject") || !is_a($Output,"ArrayObject") ) {
            return Splash::Log()->Err("Unable to perform requested action. I/O Buffer not ArrayObject Type.");
        }
        
        //====================================================================//
        // Safety Checks - Verify tasks array exists
        if ( !isset($Input->tasks) || !count($Input->tasks) ) {
            return Splash::Log()->War("Unable to perform requested action, task list is empty.");
        }
        Splash::Log()->Deb("Found " . count($Input->tasks) . " tasks in request.");
        
        //====================================================================//
        // Safety Checks - Verify Each Tasks is an ArrayObject
        foreach ($Input->tasks as $Id => $Task) {
            if ( !is_a($Task,"ArrayObject") ) {
                return Splash::Log()->Err("Unable to perform requested action. Task " . $Id . " is not ArrayObject Type.");
            }
        }
        
        //====================================================================//
        // Safety Check - Verify Router Method Exists
        if ( !method_exists($this, $Router)) {
            return Splash::Log()->Err("Unable to perform requested tasks, given router doesn't exist(" . $Router . "). Check your server configuration & methods");
        }
        
        return True;
    }
    
    /**
     *      @abstract     Execute Server Requested Tasks
     *  
     *      @param  string          $Router     Name of the router function to use for task execution
     *      @param  arrayobject     $Input      Poiner to Server Input Buffer
     *      @param  arrayobject     $Output     Poiner to Server Output Buffer
     * 
     *      @return bool            Global tesks Result
     */
    public function Execute($Router,$Input,$Output)
    {
        //====================================================================//
        // Stack Trace
        Splash::Log()->Trace(__CLASS__,__FUNCTION__);             
        
        //====================================================================//
        // Task Batch Initialisation
        $this->BatchTimer = microtime(TRUE);     // Initiate Performance Timer 

        //====================================================================//
        // Safety Checks - Validate Request
        if ( !$this->Validate($Router, $Input, $Output) ) {
            return False;
        }

        //====================================================================//
        // Init output task results
        $Output->tasks = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        
        //====================================================================//
        // Step by Step Execute Tasks
        foreach ($Input->tasks as $Id => $Task) {

            //====================================================================//
            // Tasks Execution                    
            $Output->tasks[$Id]    =   $this->ExecuteTask($Router, $Task);
            
        }
        
        //====================================================================//
        // Build Complete Task Batch Information Array
        $Output->tasksinfos              = $this->getBatchInfos();
        
        //====================================================================//
        // Return Global Batch Result
        return ($this->Count == $this->Ok)?True:False;
    }

    /**
     *      @abstract     Execute a Single Tasks
     *  
     *      @param  string          $Router     Name of the router function to use for task execution
     *      @param  arrayobject     $Task       Task To Execute
     * 
     *      @return arrayobject   Task Result ArrayObject
     */
    public function ExecuteTask($Router,$Task)
    {
        
        //====================================================================//
        // Safety Check 
        if (empty($Task)) {
            return False;
        }
        
        //====================================================================//
        // Init Tasks Timer                    
        $this->TaskTimer = microtime(TRUE);

        //====================================================================//
        // Increment Tried Tasks Counter 
        $this->Count++;
            
        //====================================================================//
        // Tasks Execution 
        try {
            $Result = $this->$Router($Task);
        } catch (Exception $exc) {
            $Result  = $this->getEmptyResponse($Task);
            Splash::Log()->Err($exc->getMessage());  
        }  
        //====================================================================//
        // Store Task Results
        if (is_a($Result,"ArrayObject") ) {
            //====================================================================//
            // Insert Task Main Informations
            $Result->id            = $Task["id"];
            //====================================================================//
            // Insert Task Performance Informations
            $Result->delayms       = $this->getDelayTaskStarted();
            $Result->delaystr      = sprintf("%.2f %s", $this->getDelayTaskStarted() , " ms");
            //====================================================================//
            // Increment Success Tasks Counter 
            if ($Result->result) {
                $this->Ok++;
            }
            
            return $Result;
        }
        return False;
    }
    
    /**
     *      @abstract     Build Complete Task Batch Information Array
     *  
     *      @return       array 
     */
    private function getBatchInfos()
    {    
        return array(
            "DelayMs"       =>  $this->getDelayStarted(),
            "DelayStr"      =>  sprintf("%.2f %s", $this->getDelayStarted(), " ms"),
            "Performed"     =>  $this->Count,
            "Ok"            =>  $this->Ok
        );
    }    
    
    /**
     *      @abstract     Delay in MilliSecond Since Router Started
     *  
     *      @return       float 
     */
    private function getDelayStarted()
    {    
        return 1000 * (microtime(TRUE) - $this->BatchTimer);
    }
    
    /**
     *      @abstract     Delay in MilliSecond Since Task Started
     *  
     *      @return       float 
     */
    private function getDelayTaskStarted()
    {    
        return 1000 * (microtime(TRUE) - $this->TaskTimer);
    }    
    
    /**
     *      @abstract     Build an Empty Task Response
     *  
     *      @param  arrayobject     $Task       Task To Execute
     * 
     *      @return arrayobject   Task Result ArrayObject
     */
    private function getEmptyResponse($Task)
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