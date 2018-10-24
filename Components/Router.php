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
use Exception;

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
    private $Count      =   0;              // Input Task Counter
    private $Success    =   0;              // Succeeded Task Counter

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
     *      @param  ArrayObject     $Input      Poiner to Server Input Buffer
     *      @param  ArrayObject     $Output     Poiner to Server Output Buffer
     *
     *      @return bool
     */
    private function validate($Router, $Input, $Output)
    {
        //====================================================================//
        // Safety Checks - Verify Inputs & Outputs are Valid
        if (!is_a($Input, "ArrayObject") || !is_a($Output, "ArrayObject")) {
            return Splash::log()->err("Unable to perform requested action. I/O Buffer not ArrayObject Type.");
        }
        
        //====================================================================//
        // Safety Checks - Verify tasks array exists
        if (!isset($Input->tasks) || !count($Input->tasks)) {
            return Splash::log()->war("Unable to perform requested action, task list is empty.");
        }
        Splash::log()->deb("Found " . count($Input->tasks) . " tasks in request.");
        
        //====================================================================//
        // Safety Checks - Verify Each Tasks is an ArrayObject
        foreach ($Input->tasks as $Id => $Task) {
            if (!is_a($Task, "ArrayObject")) {
                return Splash::log()->err(
                    "Unable to perform requested action. Task " . $Id . " is not ArrayObject Type."
                );
            }
        }
        
        //====================================================================//
        // Safety Check - Verify Router Exists
        if (!class_exists("\Splash\Router\\" . ucwords($Router))) {
            return Splash::log()->err(
                "Unable to perform requested tasks, given router doesn't exist(" . ucwords($Router) . "). "
                . "Check your server configuration & methods"
            );
        }
        
        return true;
    }
    
    /**
     *      @abstract     Execute Server Requested Tasks
     *
     *      @param  string          $Router     Name of the router function to use for task execution
     *      @param  ArrayObject     $Input      Poiner to Server Input Buffer
     *      @param  ArrayObject     $Output     Poiner to Server Output Buffer
     *
     *      @return bool            Global tesks Result
     */
    public function execute($Router, $Input, $Output)
    {
        //====================================================================//
        // Stack Trace
        Splash::log()->trace(__CLASS__, __FUNCTION__);
        //====================================================================//
        // Tasks Counters Initialisation
        $this->Count  =   0;                  // Input Task Counter
        $this->Success=   0;                  // Succeeded Task Counter
        //====================================================================//
        // Task Batch Initialisation
        $this->BatchTimer = microtime(true);     // Initiate Performance Timer
        //====================================================================//
        // Safety Checks - Validate Request
        if (!$this->validate($Router, $Input, $Output)) {
            return false;
        }
        //====================================================================//
        // Init output task results
        $Output->tasks = new ArrayObject(array(), ArrayObject::ARRAY_AS_PROPS);
        //====================================================================//
        // Step by Step Execute Tasks
        foreach ($Input->tasks as $Id => $Task) {
            //====================================================================//
            // Tasks Execution
            $Output->tasks[$Id]    =   $this->executeTask("\Splash\Router\\" . $Router, $Task);
        }
        //====================================================================//
        // Build Complete Task Batch Information Array
        $Output->tasksinfos              = $this->getBatchInfos();
        //====================================================================//
        // Return Global Batch Result
        return ($this->Count == $this->Success)?true:false;
    }

    /**
     *      @abstract     Execute a Single Tasks
     *
     *      @param  string          $Router     Name of the router function to use for task execution
     *      @param  ArrayObject     $Task       Task To Execute
     *
     *      @return ArrayObject   Task Result ArrayObject
     */
    public function executeTask($Router, $Task)
    {
        
        //====================================================================//
        // Safety Check
        if (empty($Task)) {
            return false;
        }
        
        //====================================================================//
        // Init Tasks Timer
        $this->TaskTimer = microtime(true);

        //====================================================================//
        // Increment Tried Tasks Counter
        $this->Count++;
            
        //====================================================================//
        // Tasks Execution
        try {
            $Result = $Router::Action($Task);
        } catch (Exception $exc) {
            $Result  = $this->getEmptyResponse($Task);
            Splash::log()->err($exc->getMessage() . " on File " . $exc->getFile() . " Line " . $exc->getLine() );
            Splash::log()->err($exc->getTraceAsString());
        }
        //====================================================================//
        // Store Task Results
        if (is_a($Result, "ArrayObject")) {
            //====================================================================//
            // Insert Task Main Informations
            $Result->id            = $Task["id"];
            //====================================================================//
            // Insert Task Performance Informations
            $Result->delayms       = $this->getDelayTaskStarted();
            $Result->delaystr      = sprintf("%.2f %s", $this->getDelayTaskStarted(), " ms");
            //====================================================================//
            // Increment Success Tasks Counter
            if ($Result->result) {
                $this->Success++;
            }
            
            return $Result;
        }
        return false;
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
    private function getEmptyResponse($Task)
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
            "Ok"            =>  $this->Success
        );
    }
    
    /**
     *      @abstract     Delay in MilliSecond Since Router Started
     *
     *      @return       float
     */
    private function getDelayStarted()
    {
        return 1000 * (microtime(true) - $this->BatchTimer);
    }
    
    /**
     *      @abstract     Delay in MilliSecond Since Task Started
     *
     *      @return       float
     */
    private function getDelayTaskStarted()
    {
        return 1000 * (microtime(true) - $this->TaskTimer);
    }
}
