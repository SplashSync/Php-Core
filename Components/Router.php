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

use Splash\Router\Admin;
use Splash\Router\Objects;
use Splash\Router\Widgets;
use Splash\Router\Files;

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
    private $TaskTimer;                     // Current Task Execution Start Timestamp
    
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
        // Safety Check - Verify Router Exists
        if ( !class_exists( "\Splash\Router\\" . $Router)) {
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
        // Tasks Counters Initialisation
        $this->Count  =   0;                  // Input Task Counter
        $this->Ok     =   0;                  // Succeeded Task Counter
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
            $Output->tasks[$Id]    =   $this->ExecuteTask("\Splash\Router\\" . $Router, $Task);
            
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
            $Result = $Router::Action($Task);
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
    
}

?>